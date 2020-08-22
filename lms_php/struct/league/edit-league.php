<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['league'])) {
                $gameid = sanitize_int($_POST['league']);
                if ($gameid) {

                    $html = "";
                    $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr, lms_league_supported FROM lms_league WHERE lms_league_id = :id";
                    $leaguequery = $mypdo->prepare($leaguesql);
                    $leaguequery->execute(array(
                        ':id' => $gameid
                    ));
                    $leaguecount = $leaguequery->rowCount();

                    if ($leaguecount > 0) {
                        
                        $key = $formKey->outputKey();
                        $leaguefetch = $leaguequery->fetch(PDO::FETCH_ASSOC);
                        $issupported = "";
                        if ($leaguefetch["lms_league_supported"] == 1) {
                            $issupported = "checked";
                        }
                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
									    <meta charset="UTF-8">
									    
									    <title>Edit League</title>
									    
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
									                    <h1><strong>Edit League</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

                        $html .= '			<div class="well col-md-6 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-league.php">';
                        $html .= $key;
                        $html .= '					<h3 class="text-center">Edit League</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="control-label col-sm-2" for="name">Name:</label>
																<div class="col-sm-4">
																 	<p class="form-control-static" name="name">' . $leaguefetch['lms_league_name'] . '</p>
																</div>
																<label class="control-label col-sm-2" for="abbr">Abbr:</label>
																<div class="col-sm-4">
																 	<p class="form-control-static" name="abbr">' . $leaguefetch['lms_league_abbr'] . '</p>
																</div>

															</div>

															<div class="form-group">
																<label class="control-label col-sm-2" for="osupported">Status:</label>
																<div class="col-sm-4">';
                        if ($leaguefetch['lms_league_supported'] == 1) {
                            $html .= '					<p class="form-control-static" name="oosupported">Supported</p>';
                        } else {
                            $html .= '					<p class="form-control-static" name="oosupported">Not supported</p>';
                        }

                        $html .= '					</div>
															</div>
										                    <div class="form-group">
										                    	
                                                               <label for="leaguename">New name:</label>
                    					                       <input type="text" class="form-control" id="leaguename" name="leaguename" value="' . $leaguefetch['lms_league_name'] . '"><br>
                                                               <label for="leagueabbr">New abbr (4 chrs max):</label>
                    					                       <input type="text" class="form-control" id="leagueabbr" name="leagueabbr" value="' . $leaguefetch['lms_league_abbr'] . '"><br>

                                                               <input type="checkbox" style="margin-left:20px;" name="issupported" id="issupported" value="true" ' . $issupported . ' >
                                                               <label for="issupported">&nbsp is Supported</label>
															   <input type= "hidden" name= "id" value="' . $gameid . '" />
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
														<a href="' . $myPath . 'struct/league/league-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
										window.location.href='league-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='league-main.php';
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