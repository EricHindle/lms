<?php
$myPath = '../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/mail-util.php';

sec_session_start();
$formKey = new formKey();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        header('Location: ' . $myPath . 'index.php?error=1');
    } else {
        if (isset($_POST['email'], $_POST['name'], $_POST['fromemail'], $_POST['fromname'], $_POST['bcc'], $_POST['subject'], $_POST['body'])) {
            $email = $_POST['email'];
            $name = $_POST['name'];
            $fromemail = $_POST['fromemail'];
            $fromname = $_POST['fromname'];
            $bcc = array($_POST['bcc']);
            $subject = $_POST['subject'];
            $body = $_POST['body'];
            $html = "";
            $sentOk = sendmail($email, $subject, $body, $name, $bcc, $fromemail, $fromname);
            if ($sentOk) {
                $html .= "<script>
							alert('Email sent OK');
							window.location.href='" . $myPath . "testing/emailtest.php';
						</script>";
            } else {
                $html .= "<script>
							alert('Email failed');
							window.location.href='" . $myPath . "testing/emailtest.php';
						</script>";
            }
        } else {
            $html .= "<script>
							alert('Missing values in POST');
							window.location.href='" . $myPath . "testing/emailtest.php';
						</script>";
        }
    }
} else {
    $html .= "<script>
							alert('Not a POST');
							window.location.href='" . $myPath . "testing/emailtest.php';
						</script>";
}

echo $html;

?>   