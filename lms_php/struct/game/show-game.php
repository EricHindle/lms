<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';
require $myPath . 'struct/picks/pick-functions.php';

sec_session_start();
$currentPage = 'manage';
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gameid'])) {

                $gameid = sanitize_int($_POST['gameid']);
                if ($gameid) {
                    $deadline = get_deadline_date();
                    $html = "";
                    $gamesql = "SELECT lms_game_start_wkno, lms_game_name, lms_week, lms_year, lms_game_status_text, lms_game_status, lms_week_start, lms_game_calendar FROM v_lms_game WHERE lms_game_id = :id";
                    $gamequery = $mypdo->prepare($gamesql);
                    $gamequery->execute(array(
                        ':id' => $gameid
                    ));
                    $gamecount = $gamequery->rowCount();
                  
                    if ($gamecount > 0) {
                        $game = $gamequery->fetch(PDO::FETCH_ASSOC);
                        $game_calendar = $game['lms_game_calendar'];
                        $enabled = "";
                        if ($game['lms_game_status'] > 1) {
                            $enabled = "disabled";
                        }
                        $gamename = $game['lms_game_name'];
                        $allleaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_league
                                            WHERE lms_league_supported = 1 AND lms_league_current_calendar = :cal
                                                AND lms_league_id NOT IN
                                                (SELECT lms_game_league_league_id from lms_game_league where lms_game_league_game_id = :gameid)
                                            ORDER BY lms_league_id ASC";
                        $allleaguequery = $mypdo->prepare($allleaguesql);
                        $allleaguequery->bindParam(":cal", $game_calendar, PDO::PARAM_INT);
                        $allleaguequery->bindParam(":gameid", $gameid, PDO::PARAM_INT);
                        $allleaguequery->execute();
                        $allleaguefetch = $allleaguequery->fetchAll(PDO::FETCH_ASSOC);

                        $key = $formKey->outputKey();


                        //TODO calendar
                        $remainingweeks = get_remaining_weeks(false,1);

                        $gameplayersql = "SELECT lms_game_player_status, lms_player_screen_name, lms_game_player_status_text, lms_player_id FROM v_lms_player_games WHERE lms_game_id = :game";
                        $gameplayerquery = $mypdo->prepare($gameplayersql);
                        $gameplayerquery->bindParam(":game", $gameid, PDO::PARAM_INT);
                        $gameplayerquery->execute();
                        $gameplayerfetch = $gameplayerquery->fetchAll(PDO::FETCH_ASSOC);

                        $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_game_league
                                        JOIN lms_league ON lms_game_league_league_id = lms_league_id
                                        WHERE lms_game_league_game_id = :gameid
                                        ORDER BY lms_league_id ASC";
                        $leaguequery = $mypdo->prepare($leaguesql);
                        $leaguequery->execute(array(
                            ':gameid' => $gameid
                        ));
                        $leaguefetch = $leaguequery->fetchAll(PDO::FETCH_ASSOC);
                        $key = $formKey->outputKey();
                        echo '
		<!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>Manage ' . $game['lms_game_name'] . ' - Last Man Live</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			</head>

			<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
                <div class="container">
                    <div  class="game-pick-card" style="margin-bottom: 20px; margin-top:20px;">
                        <div class="status-bar cancelled">Game Status: ' . $game['lms_game_status_text'] . '</div>
                            <table class="game-table">
                                <tr>
                                    <th>
                                        <h2>Manage ' . $game['lms_game_name'] . '</h2>
                                        <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                    </th>
                                </tr>               

                                <tr>
                                    <td>
                                        Game start week:
                                    </td>
                                    <td>
                                        ' . sprintf("%02d", $game['lms_week']) . '
                                    </td>
                                </tr>
                                <tr>                      
                                    <td>Start date:</td>
                                    <td>' . date_format(date_create($game['lms_week_start']), 'd M Y') . '</td>
                                </tr>

                        <form role="form" name ="edit" method="post" action="process-edit-game.php">';
                        $html .= $key;
                        $html .= '     
                                <div class="form-group">
                                <tr>
                                    <td colspan="2">
                                        <div class="table-columnTitle">New name:</div>
                                        <input type="text" class="form-field" id="gamename" name="gamename" value="' . $game['lms_game_name'] . '">
                                    </td>
                                </tr>';
                                    
                        if ($game['lms_game_status'] == 1) {
                                        $html .= '
                                <tr>
                                    <td colspan="2"><div  class="table-columnTitle">New start week:</div>
                                <select class="form-dropdown" id="gamestartweek" name="gamestartweek">';
                            foreach ($remainingweeks as $wk) {
                                $html .= '<option value="' . $wk['lms_week_no'] . '">' . $wk['lms_week'] . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
                            }
                                            $html .= '</select>';
                                        } 
                                        
                                        else {
                            $html .= '<input type= "hidden" name= "gamestartweek" value="' . $game['lms_game_start_wkno'] . '" />';
                        }
                        $html .= '    <input type= "hidden" name= "id" value="' . $gameid . '" />
                                
                                    </td>
                                </tr>
		                    </div>
                                <tr>
                                    <td colspan="2">
                            <div class="form-group">
                                <label for="iscancel">&nbsp Cancel this game</label>
                                <input type= "checkbox" name="iscancel" id="iscancel" value="true">
                            </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <h2>Leagues</h2>
                                        <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                    </td>
                                        </tr>
                                <tr>
                                    <td><div  class="table-columnTitle">Leagues</div></td>
                                    <td><div  class="table-columnTitle">Remove</div></td>
                                </tr>';
                        foreach ($leaguefetch as $rs) {
                            $html .= '
                                        <tr>
                                            <td>' . $rs['lms_league_name'] . '</td>
                                            <td><input type= "checkbox" style="margin-left:20px;" name="rmv-' . $rs['lms_league_id'] . '" id="rmv-' . $rs['lms_league_id'] . '" value="true"  ' . $enabled . '></td>
                                        </tr>';
                        }
                        $html .= '  
                            </table>';




                        if (! empty($allleaguefetch)) {
                            $html .= '
                            <div class="form-group" style="padding-left:3em;">
                                  <label for="addleague">Add league &nbsp;&nbsp;</label>
								  <input type= "checkbox" name= "addleague" id="addleague" value="true" />
                            </div>
                            <div class="form-group" style="padding-left:3em;">
                                <select class="form-dropdown" style="width:70%" id="leagueid" name="leagueid" ' . $enabled . '>';
                            foreach ($allleaguefetch as $myLeague) {
                                $html .= ' <option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
                            }
                            $html .= '	
                                </select>
                            </div>';
                        }


                                    // if ($gamefetch['lms_game_status'] == 2) {
                                    //     $html .= ' 
                                    //     <div style="padding:25px;text-align:left;">
                                    //         <div>
                                    //             <label class="form-text">Current week selection deadline: </label>' . date_format(date_create($deadline), 'd M Y') . '
                                    //         </div>
                                    //         ';
                                    // } else {
                                    //     $html .= '
                                    //     <div style="padding:25px;text-align:left;margin-bottom:20px;">
                                    //         ';
                                    // }
                            // $html .= ' 
                            //         <table class="table table-bordered" id="picks">
                            //             <thead>
                            //                 <tr class="info">
                            //                     <th>Player Name</th>
                            //                     <th>Player Status</th>
                            //                     <th>Current picks</th>
                            //                 </tr>
                            //             </thead>
                            //             <tbody>
                            //                 ';
                            // foreach ($gameplayerfetch as $rs) {
                            //     $pickfetch = get_current_player_pick($gameid, $rs['lms_player_id']);
                            //     $nextpickfetch = get_next_player_pick($gameid, $rs['lms_player_id']);
                            //     $currentpick = '';
                            //     $thispick = '';
                            //     $nextpick = '';
                            //     $rowcolor = 'white';
                            //     $selcolor = 'white';
                            //     if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3) {
                            //         $rowcolor = $rs['lms_game_player_status'] == 2 ? 'lightred' : 'silver';
                            //     } else {
                            //         if ($pickfetch || $nextpickfetch) {
                            //             $newline = '';
                            //             if ($pickfetch && $nextpickfetch) {
                            //                 $newline = '</br>';
                            //             }
                            //             if ($pickfetch) {
                            //                 $thispick = $pickfetch['lms_team_name'] . ' (' . date_format(date_create($pickfetch['lms_match_date']), 'd M Y') . ')';
                            //             }
                            //             if ($nextpickfetch) {
                            //                 $nextpick = $nextpickfetch['lms_team_name'] . ' (' . date_format(date_create($nextpickfetch['lms_match_date']), 'd M Y') . ')';
                            //             }
                            //             $currentpick = $thispick . $newline . $nextpick;
                            //         } else {
                            //             if ($gamefetch['lms_game_start_wkno'] <= $_SESSION['matchweek']) {
                            //                 $currentpick = '(waiting)';
                            //                 $selcolor = 'crimson';
                            //             }
                            //         }
                            //     }
                            //     $html .= '
                            //                 <tr style="color:' . $rowcolor . '">
                            //                     <td>' . $rs['lms_player_screen_name'] . '</td>
                            //                     <td>' . $rs['lms_game_player_status_text'] . '</td>
                            //                     <td style="color:' . $selcolor . '">' . $currentpick . '</td>
                            //                 </tr>';
                            // }
                            // $html .= '
                            //             </tbody>
                            //         </table>
                                // </div>
                                $html .= '<div class="form-group">
                                    <input id="submit" name="submit" type="submit" value="Update" class="btn">
					        </div>	
		                </form>
                    </div>';
                        if ($game['lms_game_status'] == 1) {
                            $html .= '
                            <div class="game-pick-card">
                            <table class="game-table">
                            <tr>
                                <th>
                                    <h2>Invite Players</h2>
                                    <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                            <form class="form-group" role="form" name ="inviteplayer" method="post" action="' . $myPath . 'struct/player/invite-player.php">';
                            $html .= $key;
                            $html .= '
                                <input type= "hidden" name= "gameid" value="' . $gameid . '" />
                                <h4>Email Addresses</h4>
                                <div class="form-group">
                                        <input type="text" class="form-field" name="email1"  id="email1" placeholder="new player 1"> <br>
                                        <input type="text" class="form-field" name="email2"  id="email2" placeholder="new player 2"> <br>
                                        <input type="text" class="form-field" name="email3"  id="email3" placeholder="new player 3"> <br>
                                        <input type="text" class="form-field" name="email4"  id="email4" placeholder="new player 4"> <br>
                                </div>
                                    <div class="form-group">
                                        <input id="submit" name="submit" type="submit" value="Invite" class="btn">
    					        </div>	
                            </form>
                                <td>
                            </tr>
                            </table>
                        </div>
                    </div>';
                        }
                        $html .= '	
                </div>
            </body>
        </html>';
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='game-manage.php';
									</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='game-manage.php';
									</script>";
                }
            } else {
                header('Location: ' . $myPath . 'index.php?error=1');
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>