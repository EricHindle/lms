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
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();
    $weeksql = "SELECT * FROM lms_week WHERE lms_year = :season and lms_week > :week ORDER BY lms_week_no ASC ";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":season", $_SESSION['currentseason'], PDO::PARAM_INT);
    $weekquery->bindParam(":week", $_SESSION['currentweek'], PDO::PARAM_INT);
    $weekquery->execute();
    $remainingweeks = $weekquery->fetchAll(PDO::FETCH_ASSOC);
    $html = "";
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
                        <h2>Game Weeks</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Add Game Weeks</h3>
			                <form role="form" name ="addweek" method="post" action="add-week.php">';
    $html .= $key;
    $html .= '	
		                    <div class="form-group"  style="padding-left:10px;text-align:left;">
                                <label class="form-text" style="display:inline-block;width:40%;text-align:left">Season:</label>
					            <input type="text" class="form-field" id="weekyear" name="weekyear" placeholder="Season YYyy" />
                                <label class="form-text" style="display:inline-block;width:40%;text-align:left">Week:</label>
					            <input type="text" class="form-field" id="weeknumber" name="weeknumber" placeholder="Week" />
                                <label class="form-text" style="display:inline-block;width:40%;text-align:left">Week start date:</label>
                                <input type="text" class="form-field" id="weekstart" name="weekstart" placeholder="yyyy-mm-dd" />
                                <label class="form-text" style="display:inline-block;width:40%;text-align:left">Number of weeks:</label>
                                <input type="text" class="form-field" id="weekcount" name="weekcount" placeholder="##" value = 1 />
				            </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Add Weeks" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
		                </form>
                    </div>
                    <div class="box" style="padding:1em;margin:10px">
    	        		<h3>Select week to edit</h3>
	                	<form class="form" role="form" name ="editweek" method="post" action="edit-week.php">';
    $html .= $key;
    $html .= '	
				        	<table class="table table-bordered" id="keywords">
								<thead>
								<tr>
									<th style="width:80px;text-align:center;">Week No.</th>
									<th style="width:80px">Season</th>
                                    <th style="width:120px">Start Date</th>
                                    <th style="width:120px">Pick Deadline</th>
                                    <th style="width:120px">End Date</th>
								</tr>
								</thead>
								<tbody>
									';
    foreach ($remainingweeks as $rs) {
        $stDate = date_format(date_create($rs['lms_week_start']), 'd-M-Y');
        $enDate = date_format(date_create($rs['lms_week_end']), 'd-M-Y');
        $dlDate = date_format(date_create($rs['lms_week_deadline']), 'd-M-Y');
        $html .= '
									<tr>
                                        <td><button type="submit" name="weekid" value="' . $rs['lms_week_no'] . '">' . $rs['lms_week'] . '</button></td>
										<td>' . $rs['lms_year'] . '</td>
										<td>' . $stDate . '</td>
										<td>' . $dlDate . '</td>
										<td>' . $enDate . '</td>
									</tr>';
    }
    $html .= '
									</tbody>
								</table>
</form>
							</div>

				        ';

    $html .= '	      		

			</body>
		</html>

		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
