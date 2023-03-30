<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */

// Can't find it being used

$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require 'game-lookup.php';

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
                    $html = "";
                    $game = get_game($gameid);
                    if ($game) {
                        $key = $formKey->outputKey();
                        $calid = $game['lms_game_calendar'];
                        $calendar = get_calendar_row($calid);
                        set_session_from_calendar($calendar);
                        $_SESSION['calendar'] = $calid;
                        $weeksql = "SELECT lms_week_no, lms_week, lms_week, lms_week_start FROM lms_week WHERE lms_week > :week AND lms_year = :season AND lms_week_calendar = :cal";
                        $weekquery = $mypdo->prepare($weeksql);
                        $weekquery->bindParam(":week", $_SESSION['currentweek'], PDO::PARAM_INT);
                        $weekquery->bindParam(":season", $_SESSION['currentseason'], PDO::PARAM_INT);
                        $weekquery->bindParam(":cal", $_SESSION['calendar'], PDO::PARAM_INT);
                        $weekquery->execute();
                        $remainingweeks = $weekquery->fetchAll(PDO::FETCH_ASSOC);
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
									                    <h1><strong>Edit Game</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

                        $html .= '			       <div class="well col-md-6 col-md-offset-1 textDark">
									                	<form class="form-horizontal" style="margin-left:24px; margin-right:30px" role="form" name ="edit" method="post" action="process-edit-game.php">';
                        $html .= $key;
                        $html .= '					       <h3 class="text-center">Edit Game</h3>
								                     	    <div class="row">
																<label class="control-label col-sm-4" for="name">Name:</label>
																<div class="col-sm-6 form-control-static" name="name">' . $game['lms_game_name'] . '
																</div>
                                                            </div>
                                                            <div class="row">
																<label class="control-label col-sm-4" for="oweek">Start week:</label>
																<div class="col-sm-4">
					                                               <p class="form-control-static" name="oweek">' . $game['lms_week'] . '</p>
				                                                </div>
                                                            </div>
                                                            <div class="row">
																<label class="control-label col-sm-4" for="odate">Start date:</label>
																<div class="col-sm-4">
					                                               <p class="form-control-static" name="odate">' . date_format(date_create($game['lms_week_start']), 'd-M-Y') . '</p>
				                                                </div>
															</div>
                                                            <br>
										                    <div class="row">
                                                               <label for="gamename">New name:</label>
                    					                       <input type="text" class="form-control" id="gamename" name="gamename" value="' . $game['lms_game_name'] . '"><br>
                                                            </div>
                                                            <div class="row">
                                                               <label for="gamestartweek">New start week:</label>
			                                                   <select class="form-control" id="gamestartweek" name="gamestartweek">';
                        foreach ($remainingweeks as $wk) {
                            $html .= '<option value="' . $wk['lms_week_no'] . '">' . $wk['lms_week'] . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
                        }
                        $html .= '	                           </select>
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
										            </div>
										        </div>
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