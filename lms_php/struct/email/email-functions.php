<?php
$myPath = "../../";
require_once $myPath . 'includes/mail-util.php';

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

function get_game($gameid)
{
    global $mypdo;
    $gamesql = "SELECT * FROM lms_game WHERE lms_game_id = :id LIMIT 1";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(":id", $gameid, PDO::PARAM_INT);
    $gamequery->execute();
    $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
    return $gamefetch;
}

function sendemailusingtemplate($templatename, $playerid, $gameid, $toEmail)
{
    global $myPath;

    $game = get_game($gameid);
    $player = get_player($playerid);
    $sentOk = false;
    $filename = $myPath . 'struct/email/templates/' . $templatename . 'template.json';
    $strJsonFileContents = file_get_contents($filename);
    if ($strJsonFileContents) {
        $array = json_decode($strJsonFileContents, true);
        $email = replacemarkers($array['toAddress'], $player, $game, $toEmail);
        $name = replacemarkers($array['toName'], $player, $game, $toEmail);
        $fromemail = replacemarkers($array['fromAddress'], $player, $game, $toEmail);
        $fromname = replacemarkers($array['fromName'], $player, $game, $toEmail);
        $bcc = replacemarkers($array['bcc'], $player, $game, $toEmail);
        $subject = replacemarkers($array['subject'], $player, $game, $toEmail);
        $body = replacemarkers($array['body'], $player, $game, $toEmail);
        $sentOk = sendmail($email, $subject, $body, $name, $bcc, $fromemail, $fromname);
    }
    return $sentOk;
}

function replacemarkers($input, $player, $game, $toEmail)
{
    $adminFromAddress = get_global_value('smtp_from_address');
    $adminFromName = get_global_value('smtp_from_name');
    $lmlurl = get_global_value('lml_url');
    
    $output = $input;
    $output = str_replace('$toEmail', $toEmail, $output);
    $output = str_replace('$email', $player['lms_player_email'], $output);
    $output = str_replace('$name', $player['lms_player_forename'] . ' ' . $player['lms_player_surname'], $output);
    $output = str_replace('$screenname', $player['lms_player_screen_name'], $output);
    $output = str_replace('$adminAddress', $adminFromAddress, $output);
    $output = str_replace('$adminName', $adminFromName, $output);
    $output = str_replace('$gameName', $game['lms_game_name'], $output);
    $output = str_replace('$gameCode', $game['lms_game_code'], $output);
    $output = str_replace('$url', $lmlurl, $output);
    $output = str_replace('$bcc', $adminFromAddress, $output);

    return $output;
}

?>