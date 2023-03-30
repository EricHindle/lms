<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require_once $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/game/game-functions.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['id'])) {
                $gameid = sanitize_int($_POST['id']);

                if ($gameid) {
                    $html = "";

                    $gamecount = check_game_exists($gameid);
                    if ($gamecount > 0) {
                        $upcount = set_game_cancelled($gameid);
                        if ($upcount > 0) {
                            sendcancelemailsforgame($gameid);
                            $html .= "<script>
											alert('Game cancelled successfully.');
											window.location.href='game-admin.php';
										</script>";
                        } else {
                            $html .= "<script>
										alert('Game NOT altered');
										window.location.href='game-admin.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='game-admin.php';
									</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
								alert('There was a problem. Please check details and try again.');
								window.location.href='game-admin.php';
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