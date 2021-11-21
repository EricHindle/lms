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
                    $player = $_SESSION['user_id'];
                    $gamename = get_game_name($gameid);
                    $gps = get_game_player_status($gameid, $player);

                    /*
                     * Get all the players picks for the selected game
                     */
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
                            break;
                        }
                    }
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

                    $html = "";
                    $key = $formKey->outputKey();
                    echo '
		<!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Game Weeks</title>
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
                    <div class="box" style="padding:1em;">
                        <h2>Selections</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px">
    	        		<h4>Game : ' . $gamename . '</h4>
';
                    $html .= $key;
                    $html .= '	
			        	<table class="table table-bordered" id="keywords">
							<thead>
    							<tr class="game">
    								<th>Game Week</th>
                                    <th>Match Date</th>
                                    <th>Team</th>
                                    <th>Outcome</th>
    							</tr>
							</thead>
							<tbody>
									';
                    foreach ($pickfetch as $rs) {
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
                                        <td>' . date_format(date_create($rs['lms_match_date']), 'd-M-Y') . '</td>
                                        <td>' . $rs['lms_team_name'] . '</td>
                                        <td>' . $result . '</td>
									</tr>';
                    }
                    $html .= '
								</tbody>
							</table>
						</div>';

                    if (time() < strtotime(get_deadline_date())) {
                        if ($gps['lms_game_player_status'] == "1") {
                            $html .= '    
                        <div class="box" style="padding:1em;margin:10px">
                            <h4 class="title" >Select a Team for week ' . $_SESSION['selectweek'] . '</h4>
					                	
		                	<form role="form" name ="editpick" method="post" action="process-pick.php">';
                            $html .= $key;
                            $html .= '
				                <div class="form-group" style="margin:12px">
		                            <select class="form-dropdown" id="matchid" name="matchid">';
                            foreach ($availfetch as $mypick) {
                                $html .= '  <option value="' . $mypick['lms_match_id'] . '">' . date_format(date_create($mypick['lms_match_date']), 'd-M') . '&nbsp;&nbsp;&nbsp;&nbsp;' . $mypick['lms_team_name'] . '</option>';
                            }
                            $html .= '</select>
                                </div>
                                <div class="col-sm-2 col-sm-offset-1">
                                    <input type= "hidden" name= "gameid" value="' . $gameid . '" />
                                    <input type= "hidden" name= "currentpickteam" value="' . $currentpickteam . '" />
                                    <input type= "hidden" name= "currentpickmatch" value="' . $currentpickmatch . '" />
                                </div>
                                <div class="form-group" style="margin-left:16px;margin-right:16px">
                                    <input id="submit" name="submit" type="submit" value="Select" class="btn graybutton" style="padding:5px;width:50%;">
                                </div>
                            </form>
                        </div>';
                        }
                    }
                    $html .= '		
                    </body>
                </html>
		';
                } else {
                    $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='pick-main.php';
									</script>";
                }

                echo $html;
            } else {
                echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='pick-main.php';
									</script>";
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
