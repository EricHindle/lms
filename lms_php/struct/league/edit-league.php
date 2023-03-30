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
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['league'])) {
                $leagueid = sanitize_int($_POST['league']);
                if ($leagueid) {

                    $html = "";
                    
                    $calrows = get_all_calendars();
                    $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr, lms_league_supported, lms_league_current_calendar FROM lms_league WHERE lms_league_id = :id";
                    $leaguequery = $mypdo->prepare($leaguesql);
                    $leaguequery->execute(array(
                        ':id' => $leagueid
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
 			    <meta charset="UTF-8">
			    <title>LML Leagues</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			</head>

			<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
                <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Edit League</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <form role="form" name ="edit" method="post" action="process-edit-league.php">';
                        $html .= $key;
                        $calname = '';
                        foreach ($calrows as $mycal) {
                            if($mycal['lms_calendar_id'] == $leaguefetch['lms_league_current_calendar']){
                                $calname = $mycal['lms_calendar_name'];
                            }
                        }
                        $html .= '
               	            <div class="form-group" style="padding:25px;text-align:left;">
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">League Name:</label>' . $leaguefetch['lms_league_name'] . '
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">Abbr:</label>' . $leaguefetch['lms_league_abbr'] . '
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">Calendar:</label>' . $calname . '
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">Status:</label>';
                        if ($leaguefetch['lms_league_supported'] == 1) {
                            $html .= 'Supported';
                        } else {
                            $html .= 'Not supported';
                        }
                        $html .= '              </div>
                            </div>
		                    <div class="form-group"  style="padding-left:10px;text-align:left;">
<div>
                                <label class="form-text" style="display:inline-block;width:40%;text-align:left">New name:</label>
                                <input type="text" class="form-field" id="leaguename" name="leaguename" value="' . $leaguefetch['lms_league_name'] . '"><br>
</div>
<div>
                                <label class="form-text"  style="display:inline-block;width:40%;text-align:left">New abbr (4 chrs max):</label>    
                                <input type="text" class="form-field" id="leagueabbr" name="leagueabbr" value="' . $leaguefetch['lms_league_abbr'] . '"><br>
</div>
    	                    	<div>
                                     <label class="form-text"  style="display:inline-block;width:40%;text-align:left">Calendar</label>
			                         <select class="form-dropdown" style="padding:10px;" id="cal" name="cal">';
                        foreach ($calrows as $mycal) {
                            $select = '';
                            if ($mycal['lms_calendar_id'] == $leaguefetch['lms_league_current_calendar'] ) {
                                $select = 'selected';
                            }
                            $html .= '   <option value="' . $mycal['lms_calendar_id'] . '" ' . $select .'>' . $mycal['lms_calendar_name'] . '</option>';
                        }
                        $html .= '	 </select>
                                </div>
<div>
                                <label class="form-text"  style="display:inline-block;width:40%;text-align:left"> </label>
                                <input type="checkbox"  name="issupported" id="issupported" value="true" ' . $issupported . ' >
                                <label for="issupported">&nbsp is Supported</label>
</div>
                                <input type= "hidden" name= "id" value="' . $leagueid . '" />
					        </div>
                            <div class="form-group" style="padding-top:25px;margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>

		                </form>


                            <div class="light-text">
					            <a href="' . $myPath . 'struct/league/league-main.php">Back</a>
					        </div>

		            </div>
		        </div>
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