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
            if (isset($_POST['id'], $_POST['startdate'], $_POST['enddate'], $_POST['deadline'])) {
                $gameid = $_POST['id'];
                $startdate = sanitize_datetime($_POST['startdate']);
                $enddate = sanitize_datetime($_POST['enddate']);
                $deadline = sanitize_datetime($_POST['deadline']);
                if ($gameid && $startdate && $enddate && $deadline) {
                    $html = "";
                    $weeksql = "SELECT lms_week_no FROM lms_week WHERE lms_week_no = :id LIMIT 1";
                    $weekquery = $mypdo->prepare($weeksql);
                    $weekquery->bindParam(':id', $gameid);
                    $weekquery->execute();
                    $weekcount = $weekquery->rowCount();
                    if ($weekcount > 0) {
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $upsql = "UPDATE lms_week SET lms_week_start = :startdt, lms_week_deadline = :deadln, lms_week_end = :enddt  WHERE lms_week_no = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $gameid);
                        $upquery->bindParam(':startdt', $startdate);
                        $upquery->bindParam(':deadln', $deadline);
                        $upquery->bindParam(':enddt', $enddate);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
                            $html .= "<script>
												alert('Details updated successfully.');
												window.location.href='week-main.php';
											</script>";
                        } else {
                            $html .= "<script>
										alert('Details not altered.');
										window.location.href='week-main.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='week-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('Missing/invalid values. Please check details and try again.');
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