<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gameid'])) {
                $id = sanitize_int($_POST['gameid']);
                if ($id) {

                    $html = "";
                    $gamesql = "SELECT lms_game_id, lms_game_name, lms_game_start_wkno, lms_week, lms_year, lms_week_start FROM v_lms_game WHERE lms_game_id = :id";
                    $gamequery = $mypdo->prepare($gamesql);
                    $gamequery->execute(array(
                        ':id' => $id
                    ));
                    $gamecount = $gamequery->rowCount();

                    if ($gamecount > 0) {
                        $key = $formKey->outputKey();
                        $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);

                        $weeksql = "SELECT lms_week_no, lms_week, lms_week, lms_week_start FROM lms_week WHERE lms_week > :week and lms_year = :season";
                        $weekquery = $mypdo->prepare($weeksql);
                        $weekquery->bindParam(":week", $_SESSION['currentweek'], PDO::PARAM_INT);
                        $weekquery->bindParam(":season", $_SESSION['currentseason'], PDO::PARAM_INT);
                        $weekquery->execute();
                        $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
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
																<div class="col-sm-4 form-control-static" name="name">' . $gamefetch['lms_game_name'] . '
																</div>
                                                            </div>
                                                            <div class="row">
																<label class="control-label col-sm-4" for="oweek">Start week:</label>
																<div class="col-sm-4">
					                                               <p class="form-control-static" name="oweek">' . $gamefetch['lms_week'] . '</p>
				                                                </div>
                                                            </div>
                                                            <div class="row">
																<label class="control-label col-sm-4" for="odate">Start date:</label>
																<div class="col-sm-4">
					                                               <p class="form-control-static" name="odate">' . date_format(date_create($gamefetch['lms_week_start']), 'd-M-Y') . '</p>
				                                                </div>
															</div>
                                                            <br>
										                    <div class="row">
                                                               <label for="gamename">New name:</label>
                    					                       <input type="text" class="form-control" id="gamename" name="gamename" value="' . $gamefetch['lms_game_name'] . '"><br>
                                                            </div>
                                                            <div class="row">
                                                               <label for="gamestartweek">New start week:</label>
			                                                   <select class="form-control" id="gamestartweek" name="gamestartweek">';
                        foreach ($weekfetch as $wk) {
                            $html .= '<option value="' . $wk['lms_week_no'] . '">' . $wk['lms_week'] . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
                        }
                        $html .= '	                           </select>
															   <input type= "hidden" name= "id" value="' . $id . '" />
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
														<a href="' . $myPath . 'struct/game/game-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
										window.location.href='game-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='game-main.php';
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