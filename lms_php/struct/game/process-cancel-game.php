<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

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

                    $gamesql = "SELECT lms_game_id, lms_game_name FROM lms_game WHERE lms_game_id = :id LIMIT 1";
                    $gamequery = $mypdo->prepare($gamesql);
                    $gamequery->execute(array(
                        ':id' => $gameid
                    ));
                    $gamecount = $gamequery->rowCount();
                    if ($gamecount > 0) {
                        $upsql = "";

                        $upsql = "UPDATE lms_game SET lms_game_status = 4 WHERE lms_game_id = :id";

                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $gameid, PDO::PARAM_INT);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
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