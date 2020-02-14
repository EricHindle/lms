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
                        $remainingweeks = get_remaining_weeks(false);

                        $gameplayersql = "SELECT lms_game_player_status, lms_player_screen_name, lms_game_player_status_text, lms_player_id FROM v_lms_player_games WHERE lms_game_id = :game";
                        $gameplayerquery = $mypdo->prepare($gameplayersql);
                        $gameplayerquery->bindParam(":game", $gameid, PDO::PARAM_INT);
                        $gameplayerquery->execute();
                        $gameplayerfetch = $gameplayerquery->fetchAll(PDO::FETCH_ASSOC);

                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
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

                                </div>';
                        if ($gamefetch['lms_game_status'] == 2) {

                            $html .= '         <div class="row">
<br>
                                    <div class="col-sm-4"><b>Current week selection deadline:</div><div class="col-sm-2"></b>  ' . date_format(date_create($deadline), 'd M Y') . '</div>
                                </div>';
                        }
                        $html .= ' <div class="row">
<br>
    					        	<table class="table table-bordered" id="keywords">
    									<thead>
    									<tr>
    										<th>Player Name</th>
    										<th>Player Status</th>
                                            <th>Current pick</th>
     									</tr>
    									</thead>
    									<tbody>
    									';

                        foreach ($gameplayerfetch as $rs) {
                            $pickfetch = get_current_player_pick($gameid, $rs['lms_player_id']);
                            $currentpick = '';
                            $rowcolor = 'black';
                            $selcolor = 'black';
                            if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3) {
                                $currentpick = '';
                                $rowcolor = 'silver';
                            } else {
                                if ($pickfetch) {
                                    $currentpick = $pickfetch['lms_team_name'] . ' (' . date_format(date_create($pickfetch['lms_match_date']), 'd M Y') . ')';
                                } else {
                                    if ($gamefetch['lms_game_start_wkno'] <= $_SESSION['matchweek']) {
                                        $currentpick = '(waiting)';
                                        $selcolor = 'crimson';
                                    }
                                }
                            }

                            if ($rs['lms_game_player_status'] > 1) {
                                $rowcolor = 'silver';
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
					<div class = "row">';
                        if ($gamefetch['lms_game_status'] < 3) {

                            $type = "text";
                            if ($gamefetch['lms_game_status'] == 2) {
                                $type = "hidden";
                            }

                            $html .= '	        <div class="well col-md-4 textDark">
                        <h3 class="text-center">Edit Game</h3>
						<form class="form-horizontal" style="margin-left:24px; margin-right:30px" role="form" name ="edit" method="post" action="process-edit-game.php">';
                            $html .= $key;
                            $html .= '					     
		                    <div class="row">
                               <label for="gamename">New name:</label>
		                       <input type="text" class="form-control" id="gamename" name="gamename" value="' . $gamefetch['lms_game_name'] . '"><br>
                            </div>
                            <div class="row">
                               <label for="gamestartweek">New start week:</label>
                               <select class="form-control" id="gamestartweek" name="gamestartweek">';
                            foreach ($remainingweeks as $wk) {
                                $html .= '<option value="' . $wk['lms_week_no'] . '">' . $wk['lms_week'] . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
                            }
                            $html .= '
	                           </select>
							   <input type= "hidden" name= "id" value="' . $gameid . '" />
		                    </div>
                            <div class="row">
                                <br>
                               <label for="iscancel">&nbsp Cancel this game</label>
                               <input type="checkbox" style="margin-left:20px;" name="iscancel" id="iscancel" value="true">
                            </div>
		                    <div class="form-group">
		                    	<br>
		                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
		                    </div>
		                </form>
		            </div>';
                        }

                        if ($gamefetch['lms_game_status'] == 1) {

                            $html .= '	                 <div class="well col-md-3 col-md-offset-1 textDark">
                        <h3 class="text-center">Invite Players</h3>
                        <form class="form-group" role="form" name ="inviteplayer" method="post" action="' . $myPath . 'struct/player/invite-player.php">';
                            $html .= $key;
                            $html .= '
                            <input type= "hidden" name= "gameid" value="' . $gameid . '" />
                            <h4>Email Addresses</h4>
                            <div class="form-group">
                                    <input type="text" class="form-control" name="email1"  id="email1" placeholder="new player 1">
                            </div>
                            <div class="form-group">
                                    <input type="text" class="form-control" name="email2"  id="email2" placeholder="new player 2">
                            </div>
                            <div class="form-group">
                                    <input type="text" class="form-control" name="email3"  id="email3" placeholder="new player 3">
                            </div>
                            <div class="form-group">
                                    <input type="text" class="form-control" name="email4"  id="email4" placeholder="new player 4">
                            </div>

		                    <div class="form-group">
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