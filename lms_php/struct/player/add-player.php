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
            if (isset($_POST['email'], $_POST['password'], $_POST['fname'], $_POST['sname'], $_POST['screenname'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];
                $fname = sanitize_message_string($_POST['fname']);
                $sname = sanitize_message_string($_POST['sname']);
                $screenname = sanitize_message_string($_POST['screenname']);
                if (isset($_POST['isadmin'])) {
                    $isadmin = $_POST['isadmin'];
                } else {
                    $isadmin = "false";
                }
                $myaccess = 0;
                if ($isadmin == 'true') {
                    $myaccess = 999;
                }
                if ($email && $password && $fname && $sname && $screenname) {
                    $html = "";
                    $cusql = "SELECT lms_player_id FROM players WHERE lms_player_email = :email LIMIT 1";
                    $cuquery = $mypdo->prepare($cusql);
                    $cuquery->execute(array(
                        ':email' => $email
                    ));
                    $cucount = $cuquery->rowCount();

                    if ($cucount > 0) {
                        $html .= "<script>
										alert('Email already in use please pick another email address.');
										window.location.href='player-main.php';
									</script>";
                    } else {

                        $name = $_SESSION['username'];
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $hash = password_hash($password, PASSWORD_DEFAULT, [
                            'cost' => 11
                        ]);
                        $sqladduser = "INSERT INTO lms_player (lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active, lms_player_created) VALUES (:username, :password, :fname, :sname, :screenname, :email, :retaccess, 1, :create)";
                        $stmtadduser = $mypdo->prepare($sqladduser);
                        $stmtadduser->bindParam(':username', $email);
                        $stmtadduser->bindParam(':password', $hash);
                        $stmtadduser->bindParam(':fname', $fname);
                        $stmtadduser->bindParam(':sname', $sname);
                        $stmtadduser->bindParam(':screenname', $screenname);
                        $stmtadduser->bindParam(':email', $email);
                        $stmtadduser->bindParam(':retaccess', $myaccess, PDO::PARAM_INT);
                        $stmtadduser->bindParam(':create', $mysqltime);
                        $stmtadduser->execute();
                        $added = $stmtadduser->rowCount();
                        $html .= "<script>
											alert('" . $added . " users added.');
											window.location.href='player-main.php';
										</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='player-main.php';
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