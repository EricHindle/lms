<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020-21 Eric Hindle. All rights reserved.
 */

$myPath = '/home/lastmanl/public_html/';
// $myPath =  "../";
require $myPath . 'includes/mail-util.php';

function sendemailusingtemplate($templatename, $playerid, $gameid, $teamid, $values, $checkflag)
{
    global $myPath;
    
    $game = get_game($gameid);
    $player = get_player($playerid);
    $team = get_team($teamid);
    $sentOk = true;
    if ($checkflag == false || $player['lms_player_send_email'] == 1) {
        $sentOk = false;
        $filename = $myPath . 'struct/email/templates/' . $templatename . 'template.json';
        $strJsonFileContents = file_get_contents($filename);
        if ($strJsonFileContents) {
            $array = json_decode($strJsonFileContents, true);
            $email = replacemarkers($array['toAddress'], $player, $game, $team, $values);
            $name = replacemarkers($array['toName'], $player, $game, $team, $values);
            $fromemail = replacemarkers($array['fromAddress'], $player, $game, $team, $values);
            $fromname = replacemarkers($array['fromName'], $player, $game, $team, $values);
            $bcc = replacemarkers($array['bcc'], $player, $game, $team, $values);
            $subject = replacemarkers($array['subject'], $player, $game, $team, $values);
            $body = replacemarkers($array['body'], $player, $game, $team, $values);
            $sentOk = sendmail($email, $subject, $body, $name, $bcc, $fromemail, $fromname);
        }
    }
    return $sentOk;
}

function replacemarkers($input, $player, $game, $team, $values)
{
    $adminFromAddress = get_global_value('smtp_from_address');
    $adminFromName = get_global_value('smtp_from_name');
    $lmlurl = get_global_value('lml_url');
    
    $output = $input;
    if (is_array($player)) {
        $output = str_replace('$email', decrypt($player['lms_player_email']), $output);
        $output = str_replace('$name', decrypt($player['lms_player_forename']) . ' ' . decrypt($player['lms_player_surname']), $output);
        $output = str_replace('$screenname', $player['lms_player_screen_name'], $output);
    }
    if (is_array($game)) {
        $output = str_replace('$gameName', $game['lms_game_name'], $output);
        $output = str_replace('$gameCode', $game['lms_game_code'], $output);
    }
     if (is_array($team)) {
        $output = str_replace('$teamName', $team['lms_team_name'], $output);
        $output = str_replace('$teamAbbr', $team['lms_team_abbr'], $output);
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