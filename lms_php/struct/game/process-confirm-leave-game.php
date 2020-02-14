<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/game/game-functions.php';
sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gameid'])) {
                $gameid = sanitize_int($_POST['gameid']);
                $playerid = $_SESSION['user_id'];
                if ($gameid && $playerid) {

                    if (remove_player_from_game($gameid, $playerid)) {

                        $html = "";

                        $html .= "<script>
								alert('You are no longer in the game');
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