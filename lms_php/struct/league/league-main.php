<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/lookup-functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$currentPage = '';
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();
    $leaguerows = get_all_leagues(true);   
    $calrows = get_all_calendars();
    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Leagues</title>
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
                        <h2>Leagues</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Add League</h3>
                        <form role="form" name ="addleague" method="post" action="add-league.php">';
    $html .= $key;
    $html .= '              <div class="form-group" style="margin:12px">
                                <div>
		                            <input type="text" class="form-field" id="leaguename" name="leaguename" placeholder="League name" />
                                </div>
    	                    	<div>
    		                        <input type="text" class="form-field" id="leagueabbr" name="leagueabbr" placeholder="Abbreviation" />
                                </div>
    	                    	<div>
                                    Calendar
			                     <select class="form-dropdown" style="padding:10px;" id="cal" name="cal">';
    foreach ($calrows as $mycal) {
        $html .= '                  <option value="' . $mycal['lms_calendar_id'] . '">' . $mycal['lms_calendar_name'] . '</option>';
    }
    $html .= '	                     </select>
                                </div>
			                </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Add League" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
					    </form>
			        </div>
                   <div class="box" style="padding:1em;margin:10px;">
                        <h3>Edit League</h3>
                        <form role="form" name ="editinfo" method="post" action="edit-league.php">';
    $html .= $key;
    $html .= ' 
			                <div class="form-group" style="margin:12px">
			                     <select class="form-dropdown" id="league" name="league">';
    foreach ($leaguerows as $myLeague) {
        $html .= '                  <option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
    }
    $html .= '	                     </select>
                            </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Select" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
                        </form>
                    </div>
                    <div class="box" style="padding:1em;padding-left:100px;margin:10px;text-align:left">
    	        		<h3>All leagues</h3>
			        	<table class="table table-bordered" id="keywords">
						  <thead>
						      <tr class="info">
									<th>Name</th>
                                    <th>Abbr</th>
									<th>Supported</th>
					          </tr>
						  </thead>
						  <tbody>
									';
    foreach ($leaguerows as $rs) {
        $supported = ($rs['lms_league_supported'] == 1 ? 'Yes' : 'No');
        $html .= '
									<tr>
										<td>' . $rs['lms_league_name'] . '</td>
                                        <td>' . $rs['lms_league_abbr'] . '</td>
										<td>' . $supported . '</td>
									</tr>';
    }
    $html .= '
						  </tbody>
                        </table>
    				</div>
                </div>
			</body>
		</html>
		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
