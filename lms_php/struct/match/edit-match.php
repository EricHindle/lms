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
$currentPage = '';
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            $matchperiod = '';
            if (isset($_POST['matchperiod'])) {
                $matchperiod = $_POST['matchperiod'];
            }
            if (isset($_POST['matchid'])) {
                $gameid = $_POST['matchid'];
                if ($gameid) {

                    $html = "";
                    $matchsql = "SELECT * FROM v_lms_match WHERE lms_match_id = :id";
                    $matchquery = $mypdo->prepare($matchsql);
                    $matchquery->execute(array(
                        ':id' => $gameid
                    ));
                    $matchcount = $matchquery->rowCount();
                    $resulttypes = get_all_result_types($mypdo);
                    if ($matchcount > 0) {
                        $key = $formKey->outputKey();
                        $matchfetch = $matchquery->fetch(PDO::FETCH_ASSOC);

                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
									    <meta charset="UTF-8">
									    
									    <title>Edit Match</title>
									    
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
									                    <h1><strong>Edit Match</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

                        $html .= '			<div class="well col-md-8 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-match.php">';
                        $html .= $key;
                        $html .= '					<h3 class="text-center">Period Dates</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="col-sm-1" for="season">Season:</label>
																<div class="col-sm-1">
																 	<p class="form-control-static" name="season" id="season">' . $matchfetch['lms_year'] . '</p>
																</div>
																<label class="col-sm-1" for="period">Week:</label>
																<div class="col-sm-1">
																 	<p class="form-control-static" name="period" id="period">' . $matchfetch['lms_week'] . '</p>
																</div>
																<label class="col-sm-1" for="team">Team:</label>
																<div class="col-sm-3">
																 	<p class="form-control-static" name="team" id="team">' . $matchfetch['lms_team_name'] . '</p>
																</div>
																<label class="col-sm-1" for="odate">Date:</label>
																<div class="col-sm-2">
																 	<p class="form-control-static" name="odate" id="odate">' . date_format(date_create($matchfetch['lms_match_date']), 'd-M-Y') . '</p>
																</div>
															</div>
										                    <div class="form-group">
                                                               <label for="matchdate">New match date:</label>
                    					                       <input type="text" class="form-control" id="matchdate" name="matchdate" value="' . date_format(date_create($matchfetch['lms_match_date']), 'Y-m-d') . '" placeholder="yyyy-mm-dd"><br>
                                                               <select class="form-control" id="result" name="result">';
                        foreach ($resulttypes as $rt) {
                            $sel = '';
                            if ($rt['lms_result_type'] == $matchfetch['lms_match_result']) {
                                $sel = 'selected';
                            }
                            $html .= ' <option ' . $sel . ' value="' . $rt['lms_result_type'] . '">' . $rt['lms_result_type_desc'] . '</option>';
                        }

                        $html .= '                           </select>
															   <input type= "hidden" name= "id" value="' . $gameid . '" />
                                                               <input type= "hidden" name= "matchperiod" value="' . $matchperiod . '" />
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
														<a href="' . $myPath . 'struct/match/match-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
														<br>
													</div>
												</div>
									      		<br><br><br><br>
									    	</div>
									    </section>
									</body>
								</html>  ';
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='match-main.php';
									</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='match-main.php';
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