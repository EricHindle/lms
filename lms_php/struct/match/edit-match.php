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
            $matchcal = 1;
            if (isset($_POST['matchperiod'])) {
                $matchperiod = $_POST['matchperiod'];
            }
            if (isset($_POST['matchcal'])) {
                $matchcal = $_POST['matchcal'];
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
 			    <meta charset="UTF-8">
			    <title>LML Games</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			</head>

			<body>';

                        include $myPath . 'globNAV.php';
                        $html .= '
                <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Edit Game</h2>
                    </div>
			                    <div class="box" style="padding:1em;margin:10px;">
                        <form role="form" name ="edit" method="post" action="process-edit-match.php">';
                        $html .= $key;
                        $html .= '<h3 class="text-center">Period Dates</h3>
							<br>
                	        <div class="form-group" style="padding:25px;text-align:left;">
                                <div>
						              <label class="form-text" style="display:inline-block;width:30%;text-align:left">Season:</label> ' . $matchfetch['lms_year'] . '
								</div>
                                <div>
									  <label class="form-text" style="display:inline-block;width:30%;text-align:left">Week:</label> ' . $matchfetch['lms_week'] . '
								</div>
                                <div>
									  <label class="form-text" style="display:inline-block;width:30%;text-align:left">Team:</label> ' . $matchfetch['lms_team_name'] . '
								</div>
                                <div>
									  <label class="form-text" style="display:inline-block;width:30%;text-align:left">Date:</label> ' . date_format(date_create($matchfetch['lms_match_date']), 'd-M-Y') . '
								</div>
		                   </div>
								<div class="form-group">
                                    <div>
                                            <label class="form-text" style="display:inline-block;width:40%;text-align:left">New match date:</label>
                    					    <input type="text" class="form-field" id="matchdate" name="matchdate" value="' . date_format(date_create($matchfetch['lms_match_date']), 'Y-m-d') . '" placeholder="yyyy-mm-dd"><br>
                                    </div>
                                    <div>
                                            <select class="form-control" id="result" name="result">';
                        foreach ($resulttypes as $rt) {
                            $sel = '';
                            if ($rt['lms_result_type'] == $matchfetch['lms_match_result']) {
                                $sel = 'selected';
                            }
                            $html .= '            <option ' . $sel . ' value="' . $rt['lms_result_type'] . '">' . $rt['lms_result_type_desc'] . '</option>';
                        }

                        $html .= '          </select>
                                    </div>
                                    <div>
										   <input type= "hidden" name= "id" value="' . $gameid . '" />
                                           <input type= "hidden" name= "matchperiod" value="' . $matchperiod . '" />
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
                            <div class="light-text">
					            <a href="' . $myPath . 'struct/match/match-main.php?cal='. $matchcal . '&matchperiod=' . $matchperiod . '">Back</a>
					        </div>
		            </div>
		        </div>
			</body>
		</html>
		';
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