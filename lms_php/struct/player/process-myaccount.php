<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['userid'], $_POST['email'], $_POST['fname'], $_POST['sname'], $_POST['screenname'])) {
                $userid = sanitize_int($_POST['userid']);
                $fname = $_POST['fname'];
                $sname = $_POST['sname'];
                $screenname = $_POST['screenname'];
                $email = $_POST['email'];
                if ($userid) {
                    $html = "";
                    $upsql = "UPDATE lms_player SET lms_player_email = :email, lms_player_forename = :forename, lms_player_surname = :surname, lms_player_screen_name = :screenname WHERE lms_player_id = :userid";
                    $upduser = $mypdo->prepare($upsql);
                    $upduser->bindParam(':forename', $fname);
                    $upduser->bindParam(':surname', $sname);
                    $upduser->bindParam(':screenname', $screenname);
                    $upduser->bindParam(':email', $email);
                    $upduser->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $upduser->execute();
                    $upcount = $upduser->rowCount();
                    if ($upcount > 0) {
                        $_SESSION['nickname'] = $screenname;
                        $_SESSION['fname'] = $fname;
                        $_SESSION['sname'] = $sname;
                        $_SESSION['email'] = $email;
                        $html .= "<script>
												alert('Details updated successfully.');
												window.location.href='" . $myPath . "menus/home.php';
											</script>";
                    } else {
                        $html .= "<script>
										alert('Record not changed');
										window.location.href='" . $myPath . "menus/home.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='" . $myPath . "menus/home.php';
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