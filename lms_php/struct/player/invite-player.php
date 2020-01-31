<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/player/player-functions.php';

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

            $game = get_game($gameid);

            $gamecode = $game['lms_game_code'];
            $gamename = $game['lms_game_name'];
            $playerid = $_SESSION['user_id'];
            $player = get_player($playerid);
            $playername = $player['lms_player_forename'] . ' ' . $player['lms_player_surname'];
            $playeremail = $player['lms_player_email'];

            if ($emails && $gamecode && $gamename) {
                $html = "";
                $bcclist = '';
                $body = 'Please join my LMS game called ' . $gamename . '. The game code is: ' . $gamecode;
                foreach ($emails as $playeremail) {
                    sendmail($playeremail, get_global_value('invite_email_subject'), $body, '', $bcclist, $playeremail, $playername);
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