<?php
/*
 * HINDLEWARE
 * Copyright (C) 2021 Eric Hindle. All rights reserved.
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

                    if (check_game_exists($gameid) == 1) {
                        remove_available_picks($gameid);
                        remove_game_league($gameid);
                        remove_game_player($gameid);
                        remove_pick($gameid);
                        remove_game($gameid);
                        $html .= "<script>
									alert('Game removal complete');
									window.location.href='game-admin.php';
								</script>";
                    } else {
                        $html .= "<script>
										alert('Game not found. Please check details and try again.');
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