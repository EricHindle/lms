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
            if (isset($_POST['weekid'])) {
                $weekid = $_POST['weekid'];
                if ($weekid) {
                    $html = "";
                    $weeksql = "SELECT * FROM lms_week WHERE lms_week_no = :id";
                    $weekquery = $mypdo->prepare($weeksql);
                    $weekquery->execute(array(
                        ':id' => $weekid
                    ));
                    $weekcount = $weekquery->rowCount();
                    if ($weekcount > 0) {
                        $key = $formKey->outputKey();
                        $remainingweeks = $weekquery->fetch(PDO::FETCH_ASSOC);
                        echo '
		<!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Teams</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			</head>

			<body>';
        include $myPath . 'globNAV.php';
        $html .= '
                <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Edit Game Week</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
					   <form role="form" name ="edit" method="post" action="process-edit-week.php">';
                        $html .= $key;
                        $html .= '					
		                    	<div class="form-group" style="padding:25px;text-align:left;">
									<label class="form-text" style="display:inline-block;width:30%;text-align:left"  for="season">Season: </label>' . $remainingweeks['lms_year'] . '
								</div>
								<div class="form-group" style="padding:25px;text-align:left;">
									<label class="form-text" style="display:inline-block;width:30%;text-align:left"  for="period">Period: </label>'. $remainingweeks['lms_week'] . '
								</div>

			                    <div class="form-group"  style="padding-left:10px;text-align:left;">
			                    	
                                   <label class="form-text" style="display:inline-block;width:40%;text-align:left" for="startdate">New start date:</label>
			                       <input type="text" class="form-field" id="startdate" name="startdate" value="' . date_format(date_create($remainingweeks['lms_week_start']), 'Y-m-d H:i:s') . '" placeholder="yyyy-mm-dd hh:mm:ss"><br>
                                   <label class="form-text" style="display:inline-block;width:40%;text-align:left" for="deadline">New deadline:</label>
			                       <input type="text" class="form-field" id="deadline" name="deadline" value="' . date_format(date_create($remainingweeks['lms_week_deadline']), 'Y-m-d H:i:s') . '" placeholder="yyyy-mm-dd hh:mm:ss"><br>
                                   <label class="form-text" style="display:inline-block;width:40%;text-align:left" for="enddate">New end date:</label>
			                       <input type="text" class="form-field" id="enddate" name="enddate" value="' . date_format(date_create($remainingweeks['lms_week_end']), 'Y-m-d H:i:s') . '" placeholder="yyyy-mm-dd hh:mm:ss"><br>

								   <input type= "hidden" name= "id" value="' . $weekid . '" />
			                    </div>
                            <div class="form-group" style="padding-top:25px;margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
			                </form>
                            <div class="light-text">
        		              <a href="' . $myPath . 'struct/week/week-main.php">Back</a>
				            </div>
			            </div>
			        </div>
	          </body>
       </html>
									            ';
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='week-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='week-main.php';
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