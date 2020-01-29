<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/picks/pick-functions.php';
require $myPath . 'struct/match/match-functions.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['matchid'], $_POST['gameid'], $_POST['currentpickteam'], $_POST['currentpickmatch'])) {
                $matchid = sanitize_int($_POST['matchid']);
                $player = $_SESSION['user_id'];
                $game = sanitize_int($_POST['gameid']);
                $currentpickteam = sanitize_int($_POST['currentpickteam']);
                $currentpickmatch = sanitize_int($_POST['currentpickmatch']);
                if ($game && $matchid && $player) {
                    $html = "";

                    if ($currentpickmatch != 0) {
                        /*
                         * Remove current pick and make team available again
                         */
                        delete_pick($player, $game, $currentpickmatch);
                        insert_available_team($player, $game, $currentpickteam);
                    }

                    if (insert_pick($player, $game, $matchid)) {
                        delete_available_team($player, $game, get_team_from_match($matchid));

                        $html .= "<script>
									alert('Selection updated successfully.');
									window.location.href='" . $myPath . "menus/home.php';
								</script>";
                    } else {
                        $html .= "<script>
									alert('Selection not saved.');
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