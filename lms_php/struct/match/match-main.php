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
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();
    $matchperiod = '';
    if (isset($_POST['matchperiod'])) {
        $matchperiod = $_POST['matchperiod'];
    }
    if (isset($_GET['matchperiod'])) {
        $matchperiod = $_GET['matchperiod'];
    }
    if (strlen($matchperiod) != 6){
         $matchperiod = '';
    }
    $tableperiod = substr($matchperiod,4,2) . '/' . substr($matchperiod,0,4);
    $tableselect = $matchperiod;
    $matchsql = "SELECT * FROM v_lms_match WHERE lms_match_weekno = :weekno ORDER BY lms_team_name, lms_match_date  ASC ";
    if ($matchperiod == '') {
        $tableperiod = $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'];
         $tableselect = $_SESSION['matchweek'] ;
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
 			    <meta charset="UTF-8">
			    <title>LML Matches</title>
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
                        <h2>Matches</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Generate Matches</h3>
			            <form role="form" name ="genmatch" method="post" action="gen-match.php">';
    $html .= $key;
    $html .= '					
				            <div class="form-group">
			                        	<label class="form-text" for="weekid">Choose period:</label>
			                            <select class="form-dropdown" id="weekid" name="weekid">';
    foreach ($remainingweeks as $myweek) {
        $html .= '                          <option value="' . $myweek['lms_week_no'] . '">' . $myweek['lms_year'] . '/' . sprintf('%02d', $myweek['lms_week']) . '&nbsp&nbsp&nbsp->&nbsp&nbsp&nbsp' . date_format(date_create($myweek['lms_week_start']), 'd-M-Y') . '</option>';
    }
    $html .= '	                         </select>
				                    </div>
                                    <div class="form-group" style="margin-left:16px;margin-right:16px">
        					            <input id="submit" name="submit" type="submit" value="Add Matches" class="btn graybutton" style="padding:5px;width:50%;">
        					        </div>
                                </form>
                            </div>
                            <div class="box" style="padding:1em;margin:10px">
                                <h3>Edit Match</h3>
			                	<form role="form" name ="editmatch" method="post" action="edit-match.php">';
    $html .= $key;
    $html .= '					
				                    <div class="form-group">
			                        	<label class="form-text" for="matchid">Choose match:</label>
			                            <select class="form-dropdown" id="matchid" name="matchid">';
    foreach ($matchfetch as $mymatch) {
        $html .= '                          <option value="' . $mymatch['lms_match_id'] . '">' . $mymatch['lms_team_name'] . '&nbsp&nbsp&nbsp->&nbsp&nbsp&nbsp' . date_format(date_create($mymatch['lms_match_date']), 'd-M-Y') . '</option>';
    }
    $html .= '	                         </select>   
                                         <input type= "hidden" name= "matchperiod" value="' . $matchperiod . '" />
				                    </div>
                                    <div class="form-group" style="margin-left:16px;margin-right:16px">
        					            <input id="submit" name="submit" type="submit" value="Select" class="btn graybutton" style="padding:5px;width:50%;">
        					        </div>
				                </form>
				            </div>
                            <div class="box" style="padding:1em;margin:10px">
                                <h3>Matches</h3>
                                <table class="table table-bordered" id="keywords">
									<thead>
    									<tr class="match">
    										<th>Week No.</th>
    										<th>Season</th>
                                            <th>Team</th>
                                            <th>Match Date</th>
                                            <th>Result</th>
    									</tr>
									</thead>
									<tbody>
									';
    foreach ($matchfetch as $rs) {

        if ($rs['lms_match_weekno'] == $tableselect) {
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
			</body>
		</html>

		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
