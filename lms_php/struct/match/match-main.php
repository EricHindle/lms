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
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();
    $matchperiod = '';
    if (isset($_POST['matchperiod'])) {
        $matchperiod = $_POST['matchperiod'];
    }
    $matchsql = "SELECT * FROM v_lms_match WHERE lms_match_weekno = :weekno ORDER BY lms_team_name, lms_match_date  ASC ";
    if ($matchperiod == '') {
        $matchsql = "SELECT * FROM v_lms_match WHERE lms_match_weekno <> :weekno ORDER BY lms_team_name, lms_match_date  ASC ";
    }
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(':weekno', $matchperiod);
    $matchquery->execute();
    $matchfetch = $matchquery->fetchAll(PDO::FETCH_ASSOC);
    $remainingweeks = get_remaining_weeks(false);
    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>Match administration</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
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
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-8">
			                    <h1><strong>Match Admin</strong></h1>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			      		</div>
                        <div class="row">';
    $html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="genmatch" method="post" action="gen-match.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Generate Matches</h3>
				                    <div class="form-group">
			                        	<label for="weekid">Choose period:</label>
			                            <select class="form-control" id="weekid" name="weekid">';
    foreach ($remainingweeks as $myweek) {
        $html .= '<option value="' . $myweek['lms_week_no'] . '">' . $myweek['lms_year'] . '/' . sprintf('%02d', $myweek['lms_week']) . '&nbsp&nbsp&nbsp->&nbsp&nbsp&nbsp' . date_format(date_create($myweek['lms_week_start']), 'd-M-Y') . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
                                </form>
                            </div>
			            ';

    $html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="editmatch" method="post" action="edit-match.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Edit Match</h3>
				                    <div class="form-group">
			                        	<label for="matchid">Choose match:</label>
			                            <select class="form-control" id="matchid" name="matchid">';
    foreach ($matchfetch as $mymatch) {
        $html .= '<option value="' . $mymatch['lms_match_id'] . '">' . $mymatch['lms_team_name'] . '&nbsp&nbsp&nbsp->&nbsp&nbsp&nbsp' . date_format(date_create($mymatch['lms_match_date']), 'd-M-Y') . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>';

    $html .= '
                        </div>
						<div class = "row">
				        	<div class="well col-md-9 textDark">
				        		<h3>Period ' . $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'] . ' matches</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="match">
										<th>Period No.</th>
										<th>Season</th>
                                        <th>Team</th>
                                        <th>Match Date</th>
                                        <th>Result</th>
									</tr>
									</thead>
									<tbody>
									';
    foreach ($matchfetch as $rs) {

        if ($rs['lms_week'] == $_SESSION['currentweek'] && $rs['lms_year'] == $_SESSION['currentseason']) {
            $result = 'no result';
            switch ($rs['lms_match_result']) {
                case 'w':
                    $result = 'won';
                    break;
                case 'l':
                    $result = 'lost';
                    break;
                case 'd':
                    $result = 'drew';
                    break;
                case 'p':
                    $result = 'postponed';
                    break;
            }

            $stDate = date_format(date_create($rs['lms_match_date']), 'd-M-Y');
            $html .= '
    									<tr>
    										<td>' . $rs['lms_week'] . '</td>
    										<td>' . $rs['lms_year'] . '</td>
                                            <td>' . $rs['lms_team_name'] . '</td>
    										<td>' . $stDate . '</td>
    										<td>' . $result . '</td>
    									</tr>';
        }
    }
    $html .= '
									</tbody>
								</table>
							</div>
						</div>

				        ';

    $html .= '	      		
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
							</div>
						</div>
			      		<br><br><br><br>
			    	</div>
			    </section>
			</body>
		</html>

		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
