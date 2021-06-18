<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020-21 Eric Hindle. All rights reserved.
 */

$myPath = '/home/lastmanl/public_html/';
// $myPath =  "../";
require $myPath . 'includes/mail-util.php';

function sendemailusingtemplate($templatename, $playerid, $gameid, $values, $checkflag)
{
    global $myPath;
    
    $game = get_game($gameid);
    $player = get_player($playerid);
    $sentOk = true;
    if ($checkflag == false || $player['lms_player_send_email'] == 1) {
        $sentOk = false;
        $filename = $myPath . 'struct/email/templates/' . $templatename . 'template.json';
        $strJsonFileContents = file_get_contents($filename);
        if ($strJsonFileContents) {
            $array = json_decode($strJsonFileContents, true);
            $email = replacemarkers($array['toAddress'], $player, $game, $values);
            $name = replacemarkers($array['toName'], $player, $game, $values);
            $fromemail = replacemarkers($array['fromAddress'], $player, $game, $values);
            $fromname = replacemarkers($array['fromName'], $player, $game, $values);
            $bcc = replacemarkers($array['bcc'], $player, $game, $values);
            $subject = replacemarkers($array['subject'], $player, $game, $values);
            $body = replacemarkers($array['body'], $player, $game, $values);
            $sentOk = sendmail($email, $subject, $body, $name, $bcc, $fromemail, $fromname);
        }
    }
    return $sentOk;
}

function replacemarkers($input, $player, $game, $values)
{
    $adminFromAddress = get_global_value('smtp_from_address');
    $adminFromName = get_global_value('smtp_from_name');
    $lmlurl = get_global_value('lml_url');
    
    $output = $input;
    if (is_array($player)) {
        $output = str_replace('$email', $player['lms_player_email'], $output);
        $output = str_replace('$name', $player['lms_player_forename'] . ' ' . $player['lms_player_surname'], $output);
        $output = str_replace('$screenname', $player['lms_player_screen_name'], $output);
    }
    if (is_array($game)) {
        $output = str_replace('$gameName', $game['lms_game_name'], $output);
        $output = str_replace('$gameCode', $game['lms_game_code'], $output);
    }
    $output = str_replace('$adminAddress', $adminFromAddress, $output);
    $output = str_replace('$adminName', $adminFromName, $output);
    $output = str_replace('$url', $lmlurl, $output);
    $output = str_replace('$bcc', $adminFromAddress, $output);
    if (is_array($values)) {
        $arr_length = count($values);
        for ($i = 0; $i < $arr_length; $i ++) {
            $output = str_replace('$value' . $i, $values[$i], $output);
        }
    }
    return $output;
}

?>