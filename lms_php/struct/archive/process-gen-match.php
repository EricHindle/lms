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
$key = $formKey->outputKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['weekid'])) {
                $weekid = $_POST['weekid'];
                $addmore = 'false';
                if (isset($_POST['addmatches'])) {
                    $addmore = $_POST['addmatches'];
                } else {
                    $addmore = false;
                }
                if ($addmore == false) {
                    $teamsql = "SELECT lms_team_name, lms_team_id, lms_team_active FROM lms_team WHERE lms_team_active = 1";
                    $teamquery = $mypdo->prepare($teamsql);
                    $teamquery->execute();
                    $teamcount = $teamquery->rowCount();
                    $html = "";
                    if ($teamcount > 0) {
                        $teamfetch = $teamquery->fetchAll(PDO::FETCH_ASSOC);
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $totaladded = 0;
                        foreach ($teamfetch as $rs) {
                            $teamid = $rs['lms_team_id'];
                            $postadd = $_POST["add-" . $teamid];
                            $postmatchdate = $_POST["mdt-" . $teamid];
                            if ($postadd == "true") {
                                $matchsql = "SELECT lms_match_id FROM lms_match WHERE lms_match_date = :kodate and lms_match_team = :team LIMIT 1";
                                $matchquery = $mypdo->prepare($matchsql);
                                $matchquery->bindParam(':kodate', $postmatchdate);
                                $matchquery->bindParam(':team', $rs['lms_team_id']);

                                $matchquery->execute();
                                $matchcount = $matchquery->rowCount();
                                if ($matchcount == 0) {
                                    $addsql = "INSERT INTO lms_match (lms_match_weekno , lms_match_team , lms_match_date , lms_match_result) VALUES (:weekno, :team, :kodate, '')";
                                    $addquery = $mypdo->prepare($addsql);
                                    $addquery->bindParam(':weekno', $weekid);
                                    $addquery->bindParam(':team', $teamid, PDO::PARAM_INT);
                                    $addquery->bindParam(':kodate', $postmatchdate);
                                    $addquery->execute();
                                    $added = $addquery->rowCount();
                                    $totaladded += $added;
                                }
                            }
                        }
                        $html .= "<script>
                                	alert('" . $totaladded . " matches added.');
                                	window.location.href='match-main.php';
                                </script>";
                    }
                } else {
                    $formKey = new formKey();
                    $key = $formKey->outputKey();
                    echo '
                    		<!doctype html>
                    		<html>
                    			<head>
                     			</head>
                    			<body>
                                  <form id ="genmore" method="post" action="gen-match.php">';
                    $html .= $key;
                    $html .= '         <input type= "hidden" name= "weekid" value="' . $weekid . '" />
                                       <input type= "hidden" name= "midweekmatches" value="true" />
                    			 </form>
                                    <script type="text/javascript">
                                        document.getElementById("genmore").submit();
                                    </script>
                    			</body>
                    		</html>';
                }
            } else {
                $html .= "<script>
								alert('There was a problem with the weekno.');
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