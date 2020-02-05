<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/email/email-functions.php';

sec_session_start();
$formKey = new formKey();
$valid = TRUE;
$passwordErr = "There was a problem. Try again.";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        patchUser($passwordErr);
    } else {
        if (isset($_POST['username'], $_POST['pwd1'], $_POST['pwd2'])) {
            $pass1 = $_POST['pwd1'];
            $pass2 = $_POST['pwd2'];
            $username = $_POST['username'];
            if ($username == $_SESSION['username']) {
                $userid = $_SESSION['user_id'];

                if ($pass1 == $pass2) {
                    if (strlen($_POST["pwd1"]) < 8) {
                        $passwordErr = "Your Password Must Contain At Least 8 Characters!";
                        $valid = FALSE;
                    } elseif (! preg_match("#[0-9]+#", $pass1)) {
                        $passwordErr = "Your Password Must Contain At Least 1 Number!";
                        $valid = FALSE;
                    } elseif (! preg_match("#[A-Z]+#", $pass1)) {
                        $passwordErr = "Your Password Must Contain At Least 1 Capital Letter!";
                        $valid = FALSE;
                    } elseif (! preg_match("#[a-z]+#", $pass1)) {
                        $passwordErr = "Your Password Must Contain At Least 1 Lowercase Letter!";
                        $valid = FALSE;
                    }
                    if ($valid == TRUE) {
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $sql2 = "UPDATE lms_player SET lms_player_password = :password WHERE lms_player_id = :id";
                        $query2 = $mypdo->prepare($sql2);
                        $hash = password_hash($pass1, PASSWORD_DEFAULT, [
                            'cost' => 10
                        ]);
                        $query2->execute(array(
                            ':password' => $hash,
                            ':id' => $userid
                        ));
                        allowUser();
                        sendemailusingtemplate('passwordchange', $userid, '', '', true);
                    } else {
                        patchUser($passwordErr);
                    }
                } else {
                    $passwordErr = "New passwords do not match";
                    patchUser($passwordErr);
                }
            } else {
                patchUser($passwordErr);
            }
        } else {
            patchUser($passwordErr);
        }
    }
} else {
    patchUser($passwordErr);
}

function patchUser($error)
{
    global $myPath;
    $html = "";
    $html .= '
				<script>
					alert("' . $error . '");
					window.location.href="' . $myPath . 'menus/home.php";
				</script>
				';
    echo $html;
}

function allowUser()
{
    global $myPath;
    $html = "";
    $html .= "<script>
					alert('Password changed. You will now be logged out.');
					window.location.href='" . $myPath . "logout.php';
				</script>";
    echo $html;
}

?>