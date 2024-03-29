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
            if (isset($_POST['infoid'], $_POST['infovalue'])) {
                $valueid = $_POST['infoid'];
                $value = $_POST['infovalue'];
                $enc = 0;
                if (isset($_POST['infoenc']) && $_POST['infoenc']) {
                    $enc = 1;
                }
                if ($valueid) {
                    $html = "";
                    $cusql = "SELECT lms_info_id FROM lms_info WHERE lms_info_id = :valueid LIMIT 1";
                    $cuquery = $mypdo->prepare($cusql);
                    $cuquery->bindParam(':valueid', $valueid);
                    $cuquery->execute();
                    $cucount = $cuquery->rowCount();
                    if ($cucount > 0) {
                        $html .= "<script>
										alert('A value with that name already exists.');
										window.location.href='info-main.php';
									</script>";
                    } else {
                        if ($enc == 1){
                            $value = encrypt($value);
                        }
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $sqladdinfo = "INSERT INTO lms_info (lms_info_id, lms_info_value, lms_info_enc) VALUES (:infoid, :infovalue, :enc)";
                        $stmtaddinfo = $mypdo->prepare($sqladdinfo);
                        $stmtaddinfo->bindParam(':infoid', $valueid);
                        $stmtaddinfo->bindParam(':infovalue', $value);
                        $stmtaddinfo->bindParam(':enc', $enc);
                        $stmtaddinfo->execute();
                        $added = $stmtaddinfo->rowCount();
                        $html .= "<script>
									alert('" . $added . " values added.');
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