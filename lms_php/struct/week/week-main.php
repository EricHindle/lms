<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020-23 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$currentPage = '';
$formKey = new formKey();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $calrows = get_all_calendars();
    $calid = $calrows[0]['lms_calendar_id'];
    $currwk = $_SESSION['currentweek'];
    $currssn = $_SESSION['currentseason'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['setcal'])) {
                $calid = $_POST['setcal'];
            }
        }
    }
    $key = $formKey->outputKey();
    $thiscal = get_calendar_row($calid);
    if ($thiscal) {
        $currwk = $thiscal['lms_calendar_current_week'];
        $currssn = $thiscal['lms_calendar_season'];
    }

    $weeksql = "SELECT * FROM lms_week WHERE lms_year = :season and lms_week > :week and lms_week_calendar = :cal ORDER BY lms_week_no ASC ";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":season", $currssn, PDO::PARAM_INT);
    $weekquery->bindParam(":week", $currwk, PDO::PARAM_INT);
    $weekquery->bindParam(":cal", $calid, PDO::PARAM_INT);
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
                        <h3>Select a Calendar</h3>
                            <form role="form" name ="selcal" method="post" action="week-main.php">';
    $html .= $key;
    $html .= '
		                    <div class="form-group"  style="padding-left:10px;text-align:left;">

    	                    	<div>
                                    <label class="form-text" style="display:inline-block;width:40%;text-align:left">Calendar</label>
			                     <select onchange="this.form.submit()" class="form-dropdown" style="padding:10px;" id="setcal" name="setcal">';
    foreach ($calrows as $mycal) {
        $selected = '';
        if ($mycal['lms_calendar_id'] == $calid) {
            $selected = 'selected';
        }
        $html .= '                  <option value="' . $mycal['lms_calendar_id'] . '" ' . $selected . '>' . $mycal['lms_calendar_name'] . '</option>';
    }
    $html .= '	                     </select>
                                </div>
					        </div>
		                </form>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Add Game Weeks</h3>
			            <form role="form" name ="addweek" method="post" action="add-week.php">';
    $html .= $key;
    $html .= '	
		                    <div class="form-group"  style="padding-left:10px;text-align:left;">
    	                    	<div>
                                     <input type= "hidden" name= "cal" value="' . $calid . '" />
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:40%;text-align:left">Week:</label>
    					            <input type="text" class="form-field" id="weeknumber" name="weeknumber" placeholder="Week" />
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:40%;text-align:left">Week start date:</label>
                                    <input type="text" class="form-field" id="weekstart" name="weekstart" placeholder="yyyy-mm-dd" />
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:40%;text-align:left">Number of weeks:</label>
                                    <input type="text" class="form-field" id="weekcount" name="weekcount" placeholder="##" value = 1 />
                                </div>
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
	                    	<div>
                                 <input type= "hidden" name= "cal" value="' . $calid . '" />
                            </div>
                            <div>
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
    if ($remainingweeks) {
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
    }
    $html .= '
									</tbody>
								</table>
                            </div>
                        </form>
				    </div>';
    $html .= '  </body>
		  </html>';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
