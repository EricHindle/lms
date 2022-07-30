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
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
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
                  		           <div class="container" style="min-height:50vh;">
                                      <div  class="box" style="padding:1em;width:400px;margin:10px;">
                                         <h2>' . $gamename . '</h2>
                                         </br>
                                         Game start week: ' . sprintf("%02d", $gamefetch['lms_week']) . '&nbsp&nbsp Start date: ' . date_format(date_create($gamefetch['lms_week_start']), 'd M Y') ;
                        $html .= '       </br>
                                         <div class = "text-center" style="background:midnightblue;color:white">' .  $gamefetch['lms_game_status_text'] . '</div>
                                         </br>
                                         Current Match week: ';
                        $html .= $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'];
                        /* If game in play */
                        if ($gamefetch['lms_game_status'] == 2) {
                            $html .= '   </br>
                                         Deadline for week &nbsp;' . sprintf('%02d', $_SESSION['currentweek'] + 1) . '&nbsp; picks is &nbsp;' . date_format(date_create($_SESSION['deadline']), 'd M Y');
                        }
                        $html .= '    </div>';
                        /* leagues */
                        $html .= '    <div  class="box" style="padding:1em;width:400px;margin:10px;">
                                         <table class="center" id="keywords">
                                            <thead>
                                                <tr>
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
                        $html .= '            </tbody>
                                         </table>
                                           </div>';
                        /* players */
                        $html .= '            <div class="box" style="padding:1em;margin:10px">
			        	                         <table class="table table-bordered center" id="keywords">
							                        <thead>
                                            		   <tr>
                        							      <th>Player Name</th>
                        								  <th>Player Status</th>
                                                          <th>Current picks</th>
                         							   </tr>
                    							    </thead>
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
                                        $thispick = $pickfetch['lms_team_name'] . ' (' . date_format(date_create($pickfetch['lms_match_date']), 'd M Y') . ')';
                                    }
                                    if ($nextpickfetch) {
                                        $nextpick = $nextpickfetch['lms_team_name'] . ' (' . date_format(date_create($nextpickfetch['lms_match_date']), 'd M Y') . ')';
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
                            					       <td>' . $rs['lms_player_screen_name'] . '</td>
                                                       <td>' . $rs['lms_game_player_status_text'] . '</td>
                                                       <td style="color:' . $selcolor . '">' . $currentpick . '</td>
                            					    </tr>';
                        }
                        $html .= '		         </tbody>
							                  </table>
						                   </div>
                                           <div class="box" style="padding:1em;margin:10px">';
                        if ($gamefetch['lms_game_status'] < 3) {
                            $html .= '	         <h3 class="text-center">Cancel Game</h3>
                						         <form class="form-horizontal" style="margin-left:24px; margin-right:30px" role="form" name ="edit" method="post" action="process-cancel-game.php">';
                            $html .= $key;
                            $html .= '
                                		              <div class="form-group" style="padding-top:25px;margin-left:16px;margin-right:16px">
				                                           <input id="submit" name="submit" type="submit" value="Cancel the Game" class="btn graybutton" style="padding:5px;width:50%;">
                                                           <input type= "hidden" name= "id" value="' . $gameid . '" />
                                		              </div>
            		                              </form>
    	                                    </div>
                                        </div>';
                        } else {
                            $html .= '	<div class = "row">
                                            <div class="well col-md-3 col-sm-3 textDark">
                                                <h3 class="text-center">Remove Game</h3>
                                                <h5 class="text-center">All details of the game will be removed permanently</h5>
    						                    <form class="form-horizontal" style="margin-left:24px; margin-right:30px" role="form" name ="edit" method="post" action="process-remove-game.php">';
                            $html .= $key;
                            $html .= '
                        		                    <div class="form-group text-center">
                                                        <input type= "hidden" name= "id" value="' . $gameid . '" />
                        		                        <input id="submit" name="submit" type="submit" value="Remove the Game" class="btn graybutton" style="padding:5px;width:50%;">
                        		                    </div>
    		                                    </form>
    	                                    </div>
                                        </div>';
                        }
                        $html .= '
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