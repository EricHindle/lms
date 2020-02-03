<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'struct/game/game-functions.php';
require $myPath . 'includes/mail-util.php';

function get_player($playerid)
{
    global $mypdo;
    $playersql = "SELECT * FROM lms_player WHERE lms_player_id = :id LIMIT 1";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->bindParam(":id", $playerid, PDO::PARAM_INT);
    $playerquery->execute();
    $playerfetch = $playerquery->fetch(PDO::FETCH_ASSOC);
    return $playerfetch;
}

function get_player_by_userid($userid)
{
    global $mypdo;
    $sql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player WHERE lms_player_login = :username LIMIT 1";
    $query = $mypdo->prepare($sql);
    $query->execute(array(
        ':username' => $userid
    ));
    $fetch = $query->fetch(PDO::FETCH_ASSOC);
    return $fetch;
}

function notify_loser($playerid, $gameid)
{
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $playername = $player['lms_player_forename'] . ' ' . $player['lms_player_surname'];
    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'The team you picked this week in the Last Man Live game ' . $gamename . ' was a loser. Sorry but you are out of the game. Why not login at ' . get_global_value('lml_url') . ' and join another game or start your own.';
    $subject = 'For you, the game is over';
    sendmail($playeremail, $subject, $body, $playername, $bcclist, get_global_value('admin_email_address'), 'LML Admin');
}

function notify_winner($playerid, $gameid)
{
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $playername = $player['lms_player_forename'] . ' ' . $player['lms_player_surname'];

    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'The team you picked this week in the Last Man Live game ' . $gamename . ' was a winner. You are still in the game. Do not forget to make a pick for the new week. Login at ' . get_global_value('lml_url');
    $subject = 'You are still in the game';
    sendmail($playeremail, $subject, $body, $playername, $bcclist, get_global_value('admin_email_address'), 'LML Admin');
}

function notify_no_pick($playerid, $gameid)
{
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $playername = $player['lms_player_forename'] . ' ' . $player['lms_player_surname'];

    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = array(
        get_global_value('admin_email_address')
    );
    $body = 'You have failed to make a pick in the Last Man Live game ' . $gamename . ' this week. Sorry but you are out of the game. Why not login at ' . get_global_value('lml_url') . ' and join another game or start your own.';
    $subject = 'You missed out';
    sendmail($playeremail, $subject, $body, $playername, $bcclist, get_global_value('admin_email_address'), 'LML Admin');
}

function createtemppassword($email)
{
    $newpwd = generate_password();
    $hash = password_hash($newpwd, PASSWORD_DEFAULT, [
        'cost' => 10
    ]);
    $player = get_player_by_userid($email);
    if ($player) {
        $playerid = $player['lms_player_id'];
        $istempalready = gettemppassword($playerid);
        if ($istempalready == 1) {
            removetemppassword($playerid);
        }
        inserttemppassword($playerid, $hash);
        sendpasswordemail($player, $newpwd);
    }
}

function gettemppassword($playerid){
    global $mypdo;
    $playersql = "SELECT * FROM lms_player_temp_password WHERE lms_player_id = :id";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->execute(array(
        ':id' => $playerid
    ));
    $playerCount = $playerquery->rowCount();
    return $playerCount;
}


function inserttemppassword($playerid, $hash)
{
    global $mypdo;
    $inssql = "INSERT INTO lms_player_temp_password (lms_player_id, lms_player_temp_password) VALUES (:player, :hash);";
    $insquery = $mypdo->prepare($inssql);
    $insquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $insquery->bindParam(':hash', $hash);
    $insquery->execute();
}

function removetemppassword($playerid)
{
    global $mypdo;
    $delsql = "DELETE FROM lms_player_temp_password WHERE lms_player_id=:player";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":player", $playerid, PDO::PARAM_INT);
    $delquery->execute();
}

function sendpasswordemail($player, $newpwd)
{
    $playeremail = $player['lms_player_email'];
    $playername = $player['lms_player_forename'] . ' ' . $player['lms_player_surname'];
    $bcclist = '';
    $body = 'A one-time temporary Last Man Live password was requested for ' . $playername . '  Login at ' . get_global_value('lml_url') . ' using your usual email and password ' . $newpwd . ' then change your password. This temporary password will not work a second time.';
    $subject = 'LML Password Request';
    sendmail($playeremail, $subject, $body, $playername, $bcclist, get_global_value('admin_email_address'), 'LML Admin');
}

?>