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
            if (isset($_POST['userid'], $_POST['email'], $_POST['fname'], $_POST['sname'], $_POST['screenname'])) {
                $userid = sanitize_int($_POST['userid']);
                $fname = encrypt(sanitize_paranoid_string($_POST['fname']));
                $sname = encrypt(sanitize_paranoid_string($_POST['sname']));
                $screenname = $_POST['screenname'];
                $email = encrypt($_POST['email']);
                $mobile = '';
                if (isset($_POST['mobile'])) {
                    $mobile = $_POST['mobile'];
                }
                $isadmin = (isset($_POST['isadmin']) ? $_POST['isadmin'] : "false");
                $isactive = (isset($_POST['isactive']) ? $_POST['isactive'] : "false");

                if ($userid) {
                    $html = "";
                    $myaccess = ($isadmin == "true" ? 999 : 0);
                    $myactive = ($isactive == "true" ? 1 : 0);
                    date_default_timezone_set('Europe/London');
                    $phptime = time();
                    $mysqltime = date("Y-m-d H:i:s", $phptime);

                    $player = get_player_by_email($email);

                    if ($player && $player['lms_player_id'] != $userid) {
                        $html .= "<script>
										alert('Email already in use please use another email address.');
										window.location.href='" . $myPath . "menus/home.php';
									</script>";
                    } else {
                        if (isset($_POST['mobile']) && strlen($_POST['mobile']) > 0) {
                            $mobile = encrypt($_POST['mobile']);
                            $player = get_player_by_mobile($mobile);
                        } else {
                            $mobile = '';
                        }
                        if ($player && $player['lms_player_id'] != $userid) {
                            $html .= "<script>
										alert('Phone number already in use please use another number.');
										window.location.href='" . $myPath . "menus/home.php';
									</script>";
                        } else {

                            $upsql = "UPDATE lms_player SET lms_player_email = :email, lms_player_login = :username, lms_player_forename = :forename,  lms_player_surname = :surname, lms_player_screen_name = :screenname, lms_player_mobile = :mobile, lms_access = :access, lms_active = :active WHERE lms_player_id = :id";
                            $upduser = $mypdo->prepare($upsql);
                            $upduser->bindParam(':username', $email);
                            $upduser->bindParam(':forename', $fname);
                            $upduser->bindParam(':surname', $sname);
                            $upduser->bindParam(':screenname', $screenname);
                            $upduser->bindParam(':email', $email);
                            $upduser->bindParam(':mobile', $mobile);
                            $upduser->bindParam(':access', $myaccess, PDO::PARAM_INT);
                            $upduser->bindParam(':active', $myactive, PDO::PARAM_INT);
                            $upduser->bindParam(':id', $userid, PDO::PARAM_INT);
                            $upduser->execute();
                            $upcount = $upduser->rowCount();
                            if ($upcount > 0) {
                                $html .= "<script>
										alert('Details updated successfully.');
										window.location.href='player-main.php';
									</script>";
                            } else {
                                $html .= "<script>
										alert('Record not changed');
										window.location.href='player-main.php';
									</script>";
                            }
                        }
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