<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022,2025 Eric Hindle. All rights reserved.
 */
require 'includes/db_connect.php';
require 'includes/functions.php';
require 'includes/verify-functions.php';
require 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();

if (login_check($mypdo) == true) {
    header('Location: logout.php');
} else {

    $_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ( isset($_GET['code'])) {
            $code = $_GET['code'];
            $verify = getemailverify($code);
            if (is_array($verify)){
                if(update_player_verified($verify['lms_verify_code'], $verify['lms_verify_player']) == 1) {
                    echo "<script>
							alert('Player email verified. You can now login.');
							window.location.href='index.php';
						  </script>";
                } else {
                      header('Location: index.php?error=4');
                }
            } else {
                 header('Location: index.php?error=4');
            }
        } else {
            header('Location: index.php?error=1');
        }
    } else {
        header('Location: index.php?error=1');
    }
   
}
?>
