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
//             $values = array($_POST['email'], $_POST['password'], $_POST['confirm'], $_POST['fname'], $_POST['sname'], $_POST['screenname']);
            echo "<script>
					alert('Account added.');
					window.location.href='" . $myPath . "index.php';
				  </script>";
            $infovalue = get_global_value('newplayertrap') + 1;
            set_global_value('newplayertrap', $infovalue, false);
//             sendemailusingtemplate('newplayertrap', '0000000', 0, 0, $values, false);
        } else {
            header('Location: ' . $myPath . 'index.php?error=1');
        }
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>