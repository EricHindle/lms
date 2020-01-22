<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gameid'])) {
                $gameid = sanitize_int($_POST['gameid']);
                $playerid = $_SESSION['user_id'];
                if ($gameid && $playerid) {

                    $sqljoingame = "INSERT INTO lms_game_player (lms_game_id, lms_player_id, lms_game_player_status) VALUES (:gameid, :playerid, 'active')";
                    $stmtjoingame = $mypdo->prepare($sqljoingame);
                    $stmtjoingame->bindParam(":gameid", $gameid, PDO::PARAM_INT);
                    $stmtjoingame->bindParam(":playerid", $playerid, PDO::PARAM_INT);
                    $stmtjoingame->execute();
                    $joincount = $stmtjoingame->rowCount();

                    if ($joincount > 0) {

                        $teamsql = "SELECT lms_team_id, lms_team_name FROM lms_team WHERE lms_team_active = 1 ORDER BY lms_team_name ASC";
                        $teamquery = $mypdo->prepare($teamsql);
                        $teamquery->execute();
                        $teamfetch = $teamquery->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($teamfetch as $rs) {
                            $sqlavailablegame = "INSERT INTO lms_available_picks (lms_available_picks_game, lms_available_picks_player_id, lms_available_picks_team) VALUES (:gameid, :playerid, :teamid)";
                            $stmtavailablegame = $mypdo->prepare($sqlavailablegame);
                            $stmtavailablegame->bindParam(":gameid", $gameid, PDO::PARAM_INT);
                            $stmtavailablegame->bindParam(":playerid", $playerid, PDO::PARAM_INT);
                            $stmtavailablegame->bindParam(":teamid", $rs['lms_team_id'], PDO::PARAM_INT);
                            $stmtavailablegame->execute();
                        }
                        $html = "";

                        $html .= "<script>
								alert('You are in the game');
								window.location.href='" . $myPath . "menus/home.php';
							</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='game-main.php';
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