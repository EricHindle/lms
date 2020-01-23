<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['matchid'], $_POST['gameid'])) {
                $matchid = sanitize_int($_POST['matchid']);
                $player = $_SESSION['user_id'];
                $game = sanitize_int($_POST['gameid']);
                $weekno = $_SESSION['currentseason'] . $_SESSION['currentweek'];
                if ($game && $matchid && $player) {
                    $html = "";
                    $upsql = "INSERT INTO lms_pick (lms_pick_player_id, lms_pick_game_id, lms_pick_match_id, lms_pick_wl) VALUES (:player, :game, :match, '');";
                    $upquery = $mypdo->prepare($upsql);
                    $upquery->bindParam(':player', $player, PDO::PARAM_INT);
                    $upquery->bindParam(':game', $game, PDO::PARAM_INT);
                    $upquery->bindParam(':match', $matchid, PDO::PARAM_INT);
                    $upquery->execute();
                    $upcount = $upquery->rowCount();
                    if ($upcount > 0) {
                        $html .= "<script>
									alert('Selection updated successfully.');
									window.location.href='pick-main.php';
								</script>";
                    } else {
                        $html .= "<script>
									alert('Selection not saved.');
									window.location.href='pick-main.php';
								</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
							alert('There was a problem. Please check details and try again.');
							window.location.href='pick-main.php';
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