<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/player/player-functions.php';

sec_session_start();
$formKey = new formKey();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        $html = "<script>
					alert('An error has occurred');
					window.location.href='" . $myPath . "index.php';
				</script>";
        echo $html;
    } else {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            createtemppassword($email);
            $html = "<script>
					alert('An email will be sent with a one-time temporary password.');
					window.location.href='" . $myPath . "index.php';
				</script>";
            echo $html;
        }
    }
}

?>