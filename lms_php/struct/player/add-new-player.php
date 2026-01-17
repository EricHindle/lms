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
$verifyurl = '/verify.php?code=';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        header('Location: ' . $myPath . 'index.php?error=1');
    } else {
        if (isset($_POST['email'], $_POST['password'], $_POST['confirm'], $_POST['fname'], $_POST['sname'], $_POST['screenname'])) {
            $_SESSION['hwkey'] = get_key();
            $_SESSION['hwiv'] = get_iv();
            $plainfname = $_POST['fname'];
            $plainsname = $_POST['sname'];
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
            if (empty(trim($screenname))) {
                $screenname = 'empty';
            }
            if ($email && $password && $confirm && $screenname) {
                $html = "";
                if ($password != $confirm) {
                    $html .= "<script>
    							alert('Passwords do not match');
    							window.location.href='new-member.php';
    						</script>";
                } else {
                    $isdupemail = get_player_by_email($email);

                    if ($isdupemail) {
                        $html .= "<script>
										alert('Email already in use please use another email address.');
										window.location.href='new-member.php';
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
										window.location.href='new-member.php';
									</script>";
                        } else {

                            if ($plainfname == $plainsname && (empty($plainsname) || strpos($plainsname, ' ') !== false)) {
                                $html .= "<script>
										alert('Forename and surname should be in separate boxes');
										window.location.href='new-member.php';
									</script>";
                            } elseif  ($plainfname == $plainsname && $screenname ==  $plainsname) {
                                $html .= "<script>
										alert('Invalid name values');
										window.location.href='new-member.php';
									</script>";
                            } elseif (empty(trim($plainfname)) && empty(trim($plainsname))) {
                                $html .= "<script>
										alert('No forename or surname supplied');
										window.location.href='new-member.php';
									</script>";
                            } else {
                                date_default_timezone_set('Europe/London');
                                $phptime = time();
                                $mysqltime = date("Y-m-d H:i:s", $phptime);
                                $hash = password_hash($password, PASSWORD_DEFAULT, [
                                    'cost' => 11
                                ]);
                                $verifycode = bin2hex(random_bytes(16));
                                $sqladduser = "INSERT INTO lms_player (lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_player_mobile, lms_access, lms_active, lms_player_send_email, lms_player_created) VALUES (:username, :password, :fname, :sname, :screenname, :email, :mobile, :retaccess, 0, :sendemail, :create)";
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
                                    $sqladdverify = "INSERT INTO lms_verify (lms_verify_code, lms_verify_player, lms_verify_email, lms_create_date, lms_verify_date, lms_verify_ok) VALUES (:code, :playerid, :email, :date, '0001-1-1', '0')";
                                    $stmtaddverify = $mypdo->prepare($sqladdverify);
                                    $stmtaddverify->bindParam(':code',$verifycode);
                                    $stmtaddverify->bindParam(':playerid',$playerid);
                                    $stmtaddverify->bindParam(':email',$email);
                                    $stmtaddverify->bindParam(':date',$mysqltime);
                                    $stmtaddverify->execute();
                                    
                                    sendemailusingtemplate('newaccount', $playerid, 0, 0, array($verifyurl,$verifycode), true);
                                }
                                sendemailusingtemplate('newplayer', $playerid, 0, 0, '', false);
                                $html .= "<script>
										alert('Account added. Email sent to confirm your email address. Click on the link to verify your account.');
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
										window.location.href='new-member.php';
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