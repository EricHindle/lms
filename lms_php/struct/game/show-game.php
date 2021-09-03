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
                        $allleaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_league
                                            WHERE lms_league_supported = 1
                                                AND lms_league_id NOT IN
                                                (SELECT lms_game_league_league_id from lms_game_league where lms_game_league_game_id = :gameid)
                                            ORDER BY lms_league_id ASC";
                        $allleaguequery = $mypdo->prepare($allleaguesql);
                        $allleaguequery->execute(array(
                            ':gameid' => $gameid
                        ));
                        $allleaguefetch = $allleaguequery->fetchAll(PDO::FETCH_ASSOC);

                        $key = $formKey->outputKey();
                        $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
                        $gamename = $gamefetch['lms_game_name'];
                        $remainingweeks = get_remaining_weeks(false);

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
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
									    <meta charset="UTF-8">
									    
									    <title>Edit Game</title>
									    
									    <meta name="viewport" content="width=device-width, initial-scale=1">
									    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
									    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
									    <script src="' . $myPath . 'js/jquery.js"></script>
									    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
									</head>

									<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
										<section id="homeSection">
									    <br><br>
									        <div class="container">
									        	<div class="row">
									                <div class="col-md-8">
									                    <h1><strong>' . $gamename . '</strong></h1>
									                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/game/game-manage.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
									      		</div>
                        <div class="row">
			            	<div class="well col-md-9  textDark">
                                <div class="row">
                                    <div class="col-sm-3"><b>Game start week: </b></div><div class="col-sm-1">' . sprintf("%02d", $gamefetch['lms_week']) . '</div>
                                    <div class="col-sm-2"><b>Start date:</b> </div><div class="col-sm-2">' . date_format(date_create($gamefetch['lms_week_start']), 'd M Y') . '</div>
                                    <div class="col-sm-2 col-sm-offset-1 text-center"  style="background:midnightblue;color:white">' . $gamefetch['lms_game_status_text'] . '</div>

                                </div>
                                <form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-game.php">';
                        $html .= $key;
                        $html .= '     </br>
                            <div class="row">
                            <div class="col-sm-3, col-md-3, col-lg-3">
		                    <div class="row" style="margin-left:0px;">
                               <label for="gamename">New name:</label>
                                <input type="text" class="form-control" id="gamename" name="gamename" value="' . $gamefetch['lms_game_name'] . '">
                            </div>
                            <div class="row" style="margin-left:0px;">
                               <label for="gamestartweek">New start week:</label>
                                <select size=' . sizeof($remainingweeks) . ' class="form-control" id="gamestartweek" name="gamestartweek">';
                        foreach ($remainingweeks as $wk) {
                            $html .= '<option value="' . $wk['lms_week_no'] . '">' . $wk['lms_week'] . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
                        }
                        $html .= '
	                           </select>
                                <input type= "hidden" name= "id" value="' . $gameid . '" />
		                    </div>

                                </div>';
                               $html .= '        <div class="col-sm-5, col-md-5, col-lg-5">
                                </br>
                                <table class="table table-bordered" id="keywords">
                                    <thead>
                                    <tr class="info">
                                    <th>Leagues</th>
                                    <th>Remove</th>
                                    </tr>
                                    </thead>
                                    <tbody>';
                        foreach ($leaguefetch as $rs) {
                            $html .= '
                                        <tr>
                                            <td>' . $rs['lms_league_name'] . '</td>
                                            <td><input type= "checkbox" style="margin-left:20px;" name="rmv-' . $rs['lms_league_id'] . '" id="rmv-' . $rs['lms_league_id'] . '" value="true" ></td>

                                        </tr>';
                        }
                        $html .= '  </tbody>
                                </table>
                              </div>
                                <div class="col-sm-3, col-md-3, col-lg-3">';

                        if (! empty($allleaguefetch)) {
                            $html .= '

  <div class="row" style="margin-left:0px;">

                                  <label for="addleague">&nbsp; Add league &nbsp;&nbsp;</label>
								  <input type= "checkbox" name= "addleague" id="addleague" value="true" />

			                     <select class="form-control col-md-6 col-sm-6" id="leagueid" name="leagueid">';
                            foreach ($allleaguefetch as $myLeague) {
                                $html .= ' <option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
                            }
                            $html .= '	</select>
                                     
                               </div>';
                        }
                        $html .= '             <div class="row" style="margin-left:0px;">
</br>
                                <label for="iscancel">&nbsp Cancel this game</label>

                                <input type= "checkbox" name="iscancel" style="margin-left:20px;" id="iscancel" value="true">
                            </div>
                                   </div>

                            </div>';

                        if ($gamefetch['lms_game_status'] == 2) {

                            $html .= '         <div class="row">
                                    <br>
                                    <div class="col-sm-4"><b>Current week selection deadline:</div><div class="col-sm-2"></b>  ' . date_format(date_create($deadline), 'd M Y') . '</div>
                                </div>';
                        }
                        $html .= ' <div class="row">
                                    <br>
                                     <div class="col-sm-11, col-md-11, col-lg-11">
    					        	<table class="table table-bordered" id="keywords">
    									<thead>
    									<tr class="info">
    										<th>Player Name</th>
    										<th>Player Status</th>
                                            <th>Current picks</th>
     									</tr>
    									</thead>
    									<tbody>
    									';

                        foreach ($gameplayerfetch as $rs) {
                            $pickfetch = get_current_player_pick($gameid, $rs['lms_player_id']);
                            $nextpickfetch = get_next_player_pick($gameid, $rs['lms_player_id']);
                            $currentpick = '';
                            $thispick = '';
                            $nextpick = '';
                            $rowcolor = 'black';
                            $selcolor = 'black';
                            if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3) {
                                $rowcolor = $rs['lms_game_player_status'] == 2 ? 'red' : 'silver';
                            } else {
                                if ($pickfetch || $nextpickfetch) {
                                    $newline = '';
                                    if ($pickfetch && $nextpickfetch){
                                        $newline = '</br>';
                                    }
                                    if ($pickfetch) {
                                        $thispick = $pickfetch['lms_team_name'] . ' (' . date_format(date_create($pickfetch['lms_match_date']), 'd M Y') . ')';
                                    }
                                    if ($nextpickfetch) {
                                        $nextpick = $nextpickfetch['lms_team_name'] . ' (' . date_format(date_create($nextpickfetch['lms_match_date']), 'd M Y') . ')';
                                    }
                                    $currentpick =  $thispick . $newline .  $nextpick;
                                } else {
                                    if ($gamefetch['lms_game_start_wkno'] <= $_SESSION['matchweek']) {
                                        $currentpick = '(waiting)';
                                        $selcolor = 'crimson';
                                    }
                                }
                            }
                            $html .= '
									<tr style="color:' . $rowcolor . '">
										<td>' . $rs['lms_player_screen_name'] . '</td>
                                        <td>' . $rs['lms_game_player_status_text'] . '</td>
                                        <td style="color:' . $selcolor . '">' . $currentpick . '</td>
									</tr>';
                        }
                        $html .= '
									</tbody>
								</table>
                              </div>
                            </div>
                                <div class="row">
	                                  <div class="col-sm-3, col-md-3, col-lg-3">
     		                          <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
		                        </div>
                            </div>
		                </form>
						</div>
                    </div>
					<div class = "row">';

                        if ($gamefetch['lms_game_status'] == 1) {

                            $html .= '	                 <div class="well col-md-7 col-md-offset-1 textDark " style="padding-top:0px">
                        <h3 class="text-center">Invite Players</h3>
                        <form class="form-group" role="form" name ="inviteplayer" method="post" action="' . $myPath . 'struct/player/invite-player.php">';
                            $html .= $key;
                            $html .= '
                            <input type= "hidden" name= "gameid" value="' . $gameid . '" />
                            <h4>Email Addresses</h4>
                            <div class="col-lg-5">
                                <div class="form-group">
                                        <input type="text" class="form-control" name="email1"  id="email1" placeholder="new player 1">
                                </div>
                                <div class="form-group">
                                        <input type="text" class="form-control" name="email2"  id="email2" placeholder="new player 2">
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="form-group">
                                        <input type="text" class="form-control" name="email3"  id="email3" placeholder="new player 3">
                                </div>
                                <div class="form-group">
                                        <input type="text" class="form-control" name="email4"  id="email4" placeholder="new player 4">
                                </div>
                            </div>
		                    <div class="form-group" style="padding-top:50px">
		                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                            </div>
                        </form>
                    </div>';
                        }
                        $html .= '		        </div>
		        <div class="row">
					<br>
					<div class="col-xs-6">
						<a href="' . $myPath . 'struct/game/game-manage.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
						<br>
					</div>
				</div>
	      		<br><br><br><br>
	    	</div>
	    </section>
	</body>
</html>
									            ';
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