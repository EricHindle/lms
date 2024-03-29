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
            if (isset($_POST['id'], $_POST['infovalue'])) {
                $gameid = $_POST['id'];
                $infovalue = $_POST['infovalue'];
                $enc = 0;
                if (isset($_POST['infoenc']) && $_POST['infoenc']) {
                    $enc = 1;
                }
                if ($gameid && $infovalue) {
                    $html = "";

                    $infosql = "SELECT lms_info_id, lms_info_value FROM lms_info WHERE lms_info_id = :id LIMIT 1";
                    $infoquery = $mypdo->prepare($infosql);
                    $infoquery->bindParam(':id', $gameid);
                    $infoquery->execute();
                    $infocount = $infoquery->rowCount();
                    if ($infocount > 0) {
                        if ($enc == 1){
                            $infovalue = encrypt($infovalue);
                        }
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $upsql = "UPDATE lms_info SET lms_info_value = :infovalue, lms_info_enc = :infoenc WHERE lms_info_id = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $gameid);
                        $upquery->bindParam(':infovalue', $infovalue);
                        $upquery->bindParam(':infoenc', $enc);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
                            $html .= "<script>
											alert('Details updated successfully.');
											window.location.href='info-main.php';
										</script>";
                        } else {
                            $html .= "<script>
										alert('Details not altered.');
										window.location.href='info-main.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='info-main.php';
									</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
								alert('There was a problem. Please check details and try again.');
								window.location.href='info-main.php';
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