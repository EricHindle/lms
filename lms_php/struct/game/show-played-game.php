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
									
								    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
								    <meta charset="UTF-8">
								    
								    <title>Show a Game</title>
								    
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
                    								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
                    							</div>
								      		</div>
                                            <div class="row">
                    			            	<div class="well col-md-9  textDark">
                                                    <div class="row">
                                                        <div class="col-sm-3"><b>Game start week: </b></div><div class="col-sm-1">' . sprintf("%02d", $gamefetch['lms_week']) . '</div>
                                                        <div class="col-sm-2"><b>Start date:</b> </div><div class="col-sm-2">' . date_format(date_create($gamefetch['lms_week_start']), 'd M Y') . '</div>
                                                        <div class="col-sm-2 col-sm-offset-1 text-center"  style="background:midnightblue;color:white">' . $gamefetch['lms_game_status_text'] . '</div>
                    
                                                    </div>';
                        if ($gamefetch['lms_game_status'] == 2) {

                            $html .= '              <div class="row">
                                                        <br>
                                                        <div class="col-sm-4">
                                                            <b>Current week selection deadline:</div><div class="col-sm-2"></b>  ' . date_format(date_create($deadline), 'd M Y') . '
                                                        </div>
                                                    </div>';
                        }

                        $html .= '             <div class="col-sm-5, col-md-5, col-lg-5">
                        </br>
                        <table class="table table-bordered" id="keywords">
                        <thead>
                        <tr class="info">
                        <th>Leagues</th>
                        </tr>
                        </thead>
                        <tbody>';
                        foreach ($leaguefetch as $rs) {
                            $html .= '
                            <tr>
                            <td>' . $rs['lms_league_name'] . '</td>
                                </tr>';
                        }
                        $html .= '  </tbody>
                                </table>
                                </div>';

                        $html .= '                  <div class="row">
                                                        <br>
                        					        	<table class="table table-bordered" id="keywords">
                        									<thead>
                        									<tr>
                        										<th>Player Name</th>
                        										<th>Player Status</th>
                                                                <th>Current picks</th>
                         									</tr>
                        									</thead>
                        									<tbody>	';
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
                                            </div>
                        		        <div class="row">
                        					<br>
                        					<div class="col-xs-6">
                        						<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
                        						<br>
                        					</div>
                        				</div>
                        	      		<br><br><br><br>
                        	    	</div>
                        	    </section>
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