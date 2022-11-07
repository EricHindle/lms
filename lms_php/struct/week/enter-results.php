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
            $html = "";
            $matchsql = "SELECT lms_match_id, lms_match_date, lms_match_result, lms_team_name FROM v_lms_match WHERE lms_match_weekno = :matchwk ORDER BY lms_team_name";
            $matchquery = $mypdo->prepare($matchsql);
            $matchquery->bindParam(':matchwk', $_SESSION['matchweek']);
            $matchquery->execute();
            $matchcount = $matchquery->rowCount();

            $weeksql = "SELECT * FROM lms_week WHERE lms_week_no = :id LIMIT 1";
            $weekquery = $mypdo->prepare($weeksql);
            $weekquery->bindParam(':id', $_SESSION['matchweek']);
            $weekquery->execute();
            $weekcount = $weekquery->rowCount();

            $week = 0;
            $year = 0;
            $resulttypes = get_all_result_types($mypdo);
            ;
            if ($weekcount > 0) {
                $key = $formKey->outputKey();
                $remainingweeks = $weekquery->fetch(PDO::FETCH_ASSOC);
                $week = $remainingweeks['lms_week'];
                $year = $remainingweeks['lms_year'];
            }

            if ($matchcount > 0) {
                $key = $formKey->outputKey();
                $matchfetch = $matchquery->fetchAll(PDO::FETCH_ASSOC);
                echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
									    <meta charset="UTF-8">
									    
									    <title>Enter Results</title>
									    
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
								                <div class="col-md-6">
								                    <h1><strong>Enter Results for Period ' . $year . '/' . sprintf('%02d', $week) . '</strong></h1>
								                </div>
                    							<div class="col-md-1">
                    								<a href="' . $myPath . 'struct/week/weekend-admin.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
                    							</div>
								      		</div>
								        	<div class = "row">';

                $html .= '			           <div class="well col-md-7 col-sm-8 textDark">
						                     <form class="form" role="form" name ="gen" method="post" action="process-results.php">';
                $html .= $key;
                $html .= '					     <h3 class="text-center">Results</h3>
                                                 <div class="form-group">
                    					        	<table class="table table-bordered" id="matchtable">
                    									<thead>
                    									<tr class="match">
                                                            <th>Team</th>
                                                            <th>Match Date</th>
                                                            <th>Result</th>
                    									</tr>
                    									</thead>
                    									<tbody>
                    									';

                foreach ($matchfetch as $rs) {

                    $md = date_create($rs['lms_match_date']);
                    $kodate = date_format($md, 'd-M-Y');
                    $html .= '
									                   <tr>
                                                            <td>' . $rs['lms_team_name'] . '</td>
            										        <td>' . $kodate . '</td>
                                                            <td><select class="form-control" name="res-' . $rs['lms_match_id'] . '" id="res-' . $rs['lms_match_id'] . '">';
                    foreach ($resulttypes as $rt) {
                        $sel = '';
                        if ($rt['lms_result_type'] == $matchfetch['lms_match_result']) {
                            $sel = 'selected';
                        }
                        $html .= ' <option ' . $sel . ' value="' . $rt['lms_result_type'] . '">' . $rt['lms_result_type_desc'] . '</option>';
                    }

                    $html .= '                           
                                                                </select></tr>';
                }
                $html .= '
                									</tbody>
                								</table>
                                            </div>
						                    <div class="form-group">
						                        <input type= "hidden" name= "weekid" value="' . $_SESSION['matchweek'] . '" />
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
										<a href="' . $myPath . 'struct/week/weekend-admin.php" class="btn btn-primary btn-lg" role="button">Back</a>
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
										alert('There are no matches for this period.');
										window.location.href='match-main.php';
									</script>";
            }

            echo $html;
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>