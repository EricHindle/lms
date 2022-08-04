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
$currentPage = '';
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

                    $gamesql = "SELECT lms_game_start_wkno, lms_game_name, lms_week, lms_year, lms_game_status_text, lms_game_status, lms_week_start FROM v_lms_game WHERE lms_game_id = :id";
                    $gamequery = $mypdo->prepare($gamesql);
                    $gamequery->execute(array(
                        ':id' => $gameid
                    ));
                    $gamecount = $gamequery->rowCount();
                    if ($gamecount > 0) {

                        $key = $formKey->outputKey();
                        $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
                        $gamename = $gamefetch['lms_game_name'];
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
                        echo '
							<!doctype html>
							<html>
                    			<head>
                     			    <meta charset="UTF-8">
                    			    <title>' . $gamename . ' - Last Man Live</title>
                    			    <meta name="viewport" content="width=device-width, initial-scale=1">
                                    <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                                    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
                    			    <script src="' . $myPath . 'js/jquery.js"></script>
                    			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
                    			    <script>
                    		            $(function(){
                    		            $(\'#keywords\').tablesorter(); 
                    		            });
                    		        </script>
                    			</head>

								<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
                                    <div class="container">
                                        <div  class="game-card" style="margin-bottom: 20px;">
                                            <div class="status-bar cancelled">Game Status: ' . $gamefetch['lms_game_status_text'] . '</div>
                                        
                                            <table style="padding-bottom: 3em;" class="game-table">
                                                <tr>
                                                    <th colspan="2" border="0">                            
                                                    <div><h2>' . $gamename . '</h2></div>
                                                    <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td width="50%">
                                                        <div class="table-columnTitle">Starting week:</div>
                                                        <div>Week: ' . sprintf("%02d", $gamefetch['lms_week']) . ' - ' . date_format(date_create($gamefetch['lms_week_start']), 'd M Y');
                        $html .= '</div>
                                                    </td>
                                                                        
                                                    <td>
                                                    </td>
                                                    
                                                    <td width="50%">
                                                        <div class="table-columnTitle">Game Status:</div>
                                                        <div> ' . $gamefetch['lms_game_status_text'] . ' </div>
                                    
                                                    </td>  
                                        
                                                </tr>
                                    
                                                <tr>
                                                    <td width="50%">
                                                    <div class="table-columnTitle">Current Week:</div>
                                                    <div>';
                        $html .= $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'];
                        $html .= ' </div>
                                                    </td>
                                                                      
                                                    <td>
                                                    </td>
                                                
                                                    <td width="50%">
                                                    
                                                    <div class="table-columnTitle">Leagues:</div>
                                                    <div><ul>';
                        foreach ($leaguefetch as $rs) {
                            $html .= '<li>';
                            $html .= $rs['lms_league_name'] . '</li>';
                        }
                        $html .= '         
                                                        </ul>
                                                            </div>                     
                                    
                                                    </td>
                                                </tr>
                                            
                                            </table>
                                    
                                        </div> ';

                        /* If game in play */
                        if ($gamefetch['lms_game_status'] == 2) {
                            $html .= '   </br>
                                         Deadline for week &nbsp;' . sprintf('%02d', $_SESSION['currentweek'] + 1) . '&nbsp; picks is &nbsp;' . date_format(date_create($_SESSION['deadline']), 'd M Y');
                        }
                        $html .= ' ';

                        // /* leagues */
                        // $html .= ' <div class="box" style="padding:1em;width:400px;margin:10px;">
                        // <table class="center" id="keywords">
                        // <thead>
                        // <tr>
                        // <th>Leagues</th>
                        // </tr>
                        // </thead>
                        // <tbody>';
                        // foreach ($leaguefetch as $rs) {
                        // $html .= '
                        // <tr>
                        // <td>' . $rs['lms_league_name'] . '</td>
                        // </tr>';
                        // }
                        // $html .= ' </tbody>
                        // </table>
                        // </div>';

                        /* players */
                        $html .= '            
                                        <div class="game-card" style="margin-bottom: 20px;">
                                    	    <table style="padding-bottom: 3em;" class="game-table" id="keywords">
                                                    <tr>
                                                    <th colspan="3" border="0">                            
                                                    <div><h2>Players</h2></div>
                                                    <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                                    </th>
                                                    </tr>                
                                                <tr>
                                                    <td width="30%"><div class="table-columnTitle">Name</div></td>
                                                    <td width="30%"><div class="table-columnTitle">Status</div></td>
                                                    <td width="40%"><div class="table-columnTitle">Pick</div></td>
                                                </tr>
                                    		<tbody>';
                        foreach ($gameplayerfetch as $rs) {
                            $pickfetch = get_current_player_pick($gameid, $rs['lms_player_id']);
                            $nextpickfetch = get_next_player_pick($gameid, $rs['lms_player_id']);
                            $currentpick = '';
                            $thispick = '';
                            $nextpick = '';
                            $rowcolor = 'white';
                            $selcolor = 'white';
                            if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3) {
                                $rowcolor = $rs['lms_game_player_status'] == 2 ? 'red' : 'silver';
                            } else {
                                if ($pickfetch || $nextpickfetch) {
                                    $newline = '';
                                    if ($pickfetch && $nextpickfetch) {
                                        $newline = '</br>';
                                    }
                                    if ($pickfetch) {
                                        $thispick = $pickfetch['lms_team_name'] . ' <span class="table-columnTitle">(' . date_format(date_create($pickfetch['lms_match_date']), 'd M') . ')</span>';
                                    }
                                    if ($nextpickfetch) {
                                        $nextpick = $nextpickfetch['lms_team_name'] . ' <span class="table-columnTitle">(' . date_format(date_create($nextpickfetch['lms_match_date']), 'd M') . ')</span>';
                                    }
                                    $currentpick = $thispick . $newline . $nextpick;
                                } else {
                                    if ($gamefetch['lms_game_start_wkno'] <= $_SESSION['matchweek']) {
                                        $currentpick = '(waiting)';
                                        $selcolor = 'crimson';
                                    }
                                }
                            }

                            $html .= '
                    				        <tr style="color:' . $rowcolor . '">
                    					       <td><b>' . $rs['lms_player_screen_name'] . '</b></td>
                                               <td>' . $rs['lms_game_player_status_text'] . '</td>
                                               <td style="color:' . $selcolor . '">' . $currentpick . '</td>
                    					    </tr>';
                        }
                        $html .= '		    </tbody>
					                  </table>
				                   </div>';
                        /* Selection history */
                        $html .= '         
                        
                                   <div class="game-card" style="margin-bottom: 20px;">
                                        <table style="padding-bottom: 3em;" class="game-table" id="keywords">
                                            <tr>
                                                <th colspan="3" border="0">                            
                                                <div><h2>Pick History</h2></div>
                                                <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                                </th>
                                            </tr> 
						                    <tr>
                                            <td width="20%"><div class="table-columnTitle">Week</div></td>
                                            <td width="20%"><div class="table-columnTitle">Date</div></td>
                                            <td width="40%"><div class="table-columnTitle">Pick</div></td>
                                            <td width="20%"><div class="table-columnTitle">Result</div></td>
            							    </tr>
                                                                                                            
                                                                                ';
                        /*
                         * Get all the players picks for the selected game
                         */
                        $player = $_SESSION['user_id'];
                        $picksql = "SELECT lms_pick_match_id, lms_match_result, lms_match_weekno, lms_week, lms_year,lms_match_date, lms_team_id, lms_team_name FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game ORDER BY lms_match_weekno";
                        $pickquery = $mypdo->prepare($picksql);
                        $pickquery->bindParam(':player', $player, PDO::PARAM_INT);
                        $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
                        $pickquery->execute();
                        $pickfetch = $pickquery->fetchAll(PDO::FETCH_ASSOC);

                        /*
                         * Get the current picked match id
                         */
                        $currentpickmatch = 0;
                        $currentpickteam = 0;

                        foreach ($pickfetch as $rs) {
                            if ($rs['lms_match_weekno'] == $_SESSION['selectweekkey']) {
                                $currentpickmatch = $rs['lms_pick_match_id'];
                                $currentpickteam = $rs['lms_team_id'];
                            }

                            $result = 'no result';
                            switch ($rs['lms_match_result']) {
                                case 'w':
                                    $result = 'win';
                                    break;
                                case 'l':
                                    $result = 'lose';
                                    break;
                                case 'd':
                                    $result = 'draw';
                                    break;
                                case 'p':
                                    $result = 'postponed';
                                    break;
                            }
                            $html .= '
            									 <tr>
            									 	 <td>' . sprintf('%02d', $rs['lms_week']) . '/' . $rs['lms_year'] . '</td>
                                                     <td>' . date_format(date_create($rs['lms_match_date']), 'd M') . '</td>
                                                     <td>' . $rs['lms_team_name'] . '</td>
                                                     <td>' . $result . '</td>
            									 </tr>';
                        }
                        $html .= '			  </tbody>
							               </table>
						                </div>';

                        /*
                         * Get all the matches for the current week featuring the available teams left in this game for the player
                         */
                        $availsql = "SELECT lms_match_id, lms_team_name, lms_match_date FROM v_lms_match where (lms_match_team in
                                    (SELECT lms_available_picks_team FROM v_lms_available_picks WHERE lms_available_picks_player_id = :player and lms_available_picks_game = :game)
                                        or lms_match_team in (SELECT lms_match_team FROM lms_match WHERE lms_match_id = :currentpick) )
                                         and lms_match_weekno = :weekno and lms_match_id <> :currentpick ORDER BY lms_team_name, lms_match_date";
                        $availquery = $mypdo->prepare($availsql);
                        $availquery->bindParam(':player', $player, PDO::PARAM_INT);
                        $availquery->bindParam(':game', $gameid, PDO::PARAM_INT);
                        $availquery->bindParam(':currentpick', $currentpickmatch, PDO::PARAM_INT);
                        $availquery->bindParam(':weekno', $_SESSION['selectweekkey']);
                        $availquery->execute();
                        $availfetch = $availquery->fetchAll(PDO::FETCH_ASSOC);

                        $gps = get_game_player_status($gameid, $player);

                        $select = 'Pick a Team';
                        if ($currentpickmatch > 0) {
                            $select = 'Change pick';
                        }

                        if (time() < strtotime(get_deadline_date())) {
                            if ($gps['lms_game_player_status'] == "1") {
                                $html .= '    
                        <div class="game-card" style="margin-bottom: 20px;">
                            <div style="margin:20px;">
    		                	<form role="form" name ="editpick" method="post" action="' . $myPath . 'struct/picks/process-pick.php">
                                    <h4 class="title" >' . $select . ' for week ' . $_SESSION['selectweek'] . '</h4>';
                                    $html .= $key;
                                    $html .= '
    				                <div class="form-group" style="margin:12px">
    		                            <select class="form-dropdown" id="matchid" name="matchid">';
                                    foreach ($availfetch as $mypick) {
                                        $html .= '  <option value="' . $mypick['lms_match_id'] . '">' . date_format(date_create($mypick['lms_match_date']), 'd-M') . '&nbsp;&nbsp;&nbsp;&nbsp;' . $mypick['lms_team_name'] . '</option>';
                                    }
                                    $html .= '</select>
                                    </div>
                                    <div>
                                        <input type= "hidden" name= "gameid" value="' . $gameid . '" />
                                        <input type= "hidden" name= "currentpickteam" value="' . $currentpickteam . '" />
                                        <input type= "hidden" name= "currentpickmatch" value="' . $currentpickmatch . '" />
                                    </div>
                                    <div class="form-group" style="margin-left:16px;margin-right:16px">
                                        <input id="submit" name="submit" type="submit" value="Select" class="btn graybutton" style="padding:5px;width:50%;">
                                    </div>
                                </form>
                            </div>
                        </div>';
                            }
                        }

                        $html .= '
                    </div>
                </div>
            </body>
        </html>';
                    } else {
                        $html .= "      <script>
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