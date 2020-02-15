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
        if (isset($_POST['gameid'])) {
            $gameid = $_POST['gameid'];
            $emails = array(
                $_POST['email1'],
                $_POST['email2'],
                $_POST['email3'],
                $_POST['email4']
            );
            $playerid = $_SESSION['user_id'];
            if ($emails && $gameid && $playerid) {
                $html = "";
                foreach ($emails as $inviteEmail) {
                    if ($inviteEmail != '') {
                        sendemailusingtemplate('invitation', $playerid, $gameid, array(
                            $inviteEmail
                        ), false);
                    }
                }
                $html .= "<script>
								alert('Invitations sent.');
								window.location.href='" . $myPath . "struct/game/game-manage.php';
							</script>";
            } else {}
            echo $html;
        } else {
            echo "<script>
						alert('There was a problem. Please check details and try again.');
						window.location.href='new-player.php';
					</script>";
        }
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}

?>