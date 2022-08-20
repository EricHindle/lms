<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/email/email-functions.php';

sec_session_start();
$formKey = new formKey();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        header('Location: ' . $myPath . 'index.php?error=1');
    } else {
        if (isset($_POST['email'], $_POST['password'], $_POST['confirm'], $_POST['fname'], $_POST['sname'], $_POST['screenname'])) {
            $_SESSION['hwkey'] = get_key();
            $_SESSION['hwiv'] = get_iv();
            $email = encrypt($_POST['email']);
            $password = $_POST['password'];
            $confirm = $_POST['confirm'];
            $fname = encrypt(sanitize_message_string($_POST['fname']));
            $sname = encrypt(sanitize_message_string($_POST['sname']));
            $screenname = sanitize_message_string($_POST['screenname']);
            $issendemail = (isset($_POST['issendemail']) ? $_POST['issendemail'] : false);
            $sendemail = ($issendemail ? 1 : 0);
            $myaccess = 0;
            $isdupemail = false;
            $isdupmobile = false;
            if ($email && $password && $confirm && $fname && $sname && $screenname) {
                $html = "";

                if ($password != $confirm) {
                    $html .= "<script>
    							alert('Passwords do not match');
    							window.location.href='new-player.php';
    						</script>";
                } else {
                    $isdupemail = get_player_by_email($email);

                    if ($isdupemail) {
                        $html .= "<script>
										alert('Email already in use please use another email address.');
										window.location.href='new-player.php';
									</script>";
                    } else {
                        if (isset($_POST['mobile']) && strlen($_POST['mobile']) > 0) {
                            $mobile = encrypt($_POST['mobile']);
                            
                            $isdupmobile = get_player_by_mobile($mobile);
                            
                        } else {
                            $mobile = '';
                        }
                        if ($isdupmobile) {
                            $html .= "<script>
										alert('Phone number already in use please use another number.');
										window.location.href='new-player.php';
									</script>";
                        } else {

                            if ($fname == $sname && (empty($sname) || strpos($sname, ' ') !== false)) {
                                $html .= "<script>
										alert('Forename and surname should be in separate boxes');
										window.location.href='new-player.php';
									</script>";
                            } else {
                                date_default_timezone_set('Europe/London');
                                $phptime = time();
                                $mysqltime = date("Y-m-d H:i:s", $phptime);
                                $hash = password_hash($password, PASSWORD_DEFAULT, [
                                    'cost' => 11
                                ]);
                                $sqladduser = "INSERT INTO lms_player (lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_player_mobile, lms_access, lms_active, lms_player_send_email, lms_player_created) VALUES (:username, :password, :fname, :sname, :screenname, :email, :mobile, :retaccess, 1, :sendemail, :create)";
                                $stmtadduser = $mypdo->prepare($sqladduser);
                                $stmtadduser->bindParam(':username', $email);
                                $stmtadduser->bindParam(':password', $hash);
                                $stmtadduser->bindParam(':fname', $fname);
                                $stmtadduser->bindParam(':sname', $sname);
                                $stmtadduser->bindParam(':screenname', $screenname);
                                $stmtadduser->bindParam(':email', $email);
                                $stmtadduser->bindParam(':mobile', $mobile);
                                $stmtadduser->bindParam(':retaccess', $myaccess, PDO::PARAM_INT);
                                $stmtadduser->bindParam(':sendemail', $sendemail, PDO::PARAM_INT);
                                $stmtadduser->bindParam(':create', $mysqltime);
                                $stmtadduser->execute();
                                $added = $stmtadduser->rowCount();
                                if ($added == 1) {
                                    $playerid = $mypdo->lastInsertId();
                                    sendemailusingtemplate('newaccount', $playerid, 0, 0, '', true);
                                }
                                $html .= "<script>
    											alert('Account added.');
    											window.location.href='" . $myPath . "index.php';
    										</script>";
                            }
                        }
                    }
                }

                echo $html;
            } else {
                echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='new-player.php';
									</script>";
            }
        } else {
            header('Location: ' . $myPath . 'index.php?error=1');
        }
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>