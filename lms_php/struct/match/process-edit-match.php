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
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['id'], $_POST['matchdate'], $_POST['result'])) {
                $gameid = $_POST['id'];
                $matchdate = sanitize_datetime($_POST['matchdate']);
                $result = $_POST['result'];
                $matchperiod = '';
                if (isset($_POST['matchperiod'])) {
                    $matchperiod = $_POST['matchperiod'];
                }
                if ($gameid && $matchdate) {
                    $html = "";

                    $matchsql = "SELECT lms_match_id, lms_match_calendar FROM lms_match WHERE lms_match_id = :id LIMIT 1";
                    $matchquery = $mypdo->prepare($matchsql);
                    $matchquery->bindParam(':id', $gameid);
                    $matchquery->execute();
                    $matchfetch = $matchquery->fetch(PDO::FETCH_ASSOC);
                    $matchcount = $matchquery->rowCount();

                    if ($matchcount > 0) {
                        $cal = $matchfetch['lms_match_calendar'];
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $upsql = "UPDATE lms_match SET lms_match_date = :matchdt, lms_match_result = :result WHERE lms_match_id = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $gameid);
                        $upquery->bindParam(':matchdt', $matchdate);
                        $upquery->bindParam(':result', $result);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
                            $html .= "<script>
											alert('Details updated successfully.');
											window.location.href='match-main.php?matchperiod=" . $matchperiod . "&cal=" . $cal . "';
										</script>";
                        } else {
                            $html .= "<script>
										alert('Details not altered.');
										window.location.href='match-main.php?matchperiod=" . $matchperiod . "&cal=" . $cal . "';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='match-main.php';
									</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
							alert('Missing/invalid values. Please check details and try again.');
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