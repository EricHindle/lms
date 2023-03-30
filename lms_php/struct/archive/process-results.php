<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/match/match-functions.php';
require $myPath . 'struct/week/week-functions.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['weekid'])) {
                $gameid = $_POST['weekid'];
                $matchsql = "SELECT lms_match_id, lms_match_date, lms_match_result, lms_team_name FROM v_lms_match WHERE lms_match_weekno = :matchwk";
                $matchquery = $mypdo->prepare($matchsql);
                $matchquery->bindParam(':matchwk', $gameid);
                $matchquery->execute();
                $matchcount = $matchquery->rowCount();
                $html = "";
                if ($matchcount > 0) {
                    $matchfetch = $matchquery->fetchAll(PDO::FETCH_ASSOC);
                    date_default_timezone_set('Europe/London');
                    $phptime = time();
                    $mysqltime = date("Y-m-d H:i:s", $phptime);
                    $totalupdates = 0;
                    foreach ($matchfetch as $rs) {
                        $matchid = $rs['lms_match_id'];
                        $matchresult = $_POST["res-" . $matchid];
                        $upsql = "UPDATE lms_match SET lms_match_result = :result WHERE lms_match_id = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $matchid);
                        $upquery->bindParam(':result', $matchresult);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        $totalupdates += $upcount;
                    }
                    
                    $missingresultct = get_count_of_matches_with_no_result();
                    if ($missingresultct == 0) {
                        update_complete($_SESSION['matchweek']);
                    }
                    $html .= "<script>
                            	alert('" . $totalupdates . " teams resulted.');
                            	window.location.href='" . $myPath . "struct/week/weekend-admin.php';
                            </script>";
                } else {
                    $html .= "<script>
    								alert('No teams found with matches this week.');
    								window.location.href='" . $myPath . "struct/week/weekend-admin.php';
    							</script>";
                }
            } else {
                $html .= "<script>
								alert('There was a problem with the week number');
								window.location.href='" . $myPath . "struct/week/weekend-admin.php';
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