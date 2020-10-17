<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */

require '/home/lastmanl/public_html/includes/db_connect.php';
require '/home/lastmanl/public_html/scheduled/email-functions.php'; 

function activateGames($nextgameweek)
{
    global $mypdo;
    $updgamesql = "UPDATE lms_game SET lms_game_status = 2, lms_game_week_count = 1 WHERE lms_game_start_wkno = :weekno";
    $updgamequery = $mypdo->prepare($updgamesql);
    $updgamequery->bindParam(':weekno', $nextgameweek);
    $updgamequery->execute();
    $upCount = $updgamequery->rowCount();
    return $upCount;
}

function check_start_date()
{
    global $mypdo;
    $today = date("Y-m-d");
    $weeksql = "SELECT * FROM lms_week WHERE lms_week_start = :today LIMIT 1";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":today", $today);
    $weekquery->execute();
    $rowcount = $weekquery->rowCount();
    return $rowcount;
}


function get_active_games()
{
    global $mypdo;
    $gamesql = "SELECT * FROM lms_game WHERE lms_game_status = 2";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->execute();
    $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
    return $gamefetch;
}

function get_count_of_matches_with_no_result()
{
    global $mypdo;
    $selectsql = "SELECT lms_match_id, lms_team_name FROM v_lms_match WHERE lms_match_weekno = :weekno and lms_match_result = ''";
    $selectquery = $mypdo->prepare($selectsql);
    $selectquery->execute(array(
        ':weekno' => $_SESSION['matchweek']
    ));
    $selectcount = $selectquery->rowCount();
    return $selectcount;
}

function get_current_deadline_date($selectweekkey)
{
    global $mypdo;
    $weeksql = "SELECT lms_week_deadline FROM lms_week WHERE lms_week_no = :week LIMIT 1";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $selectweekkey, PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetch(PDO::FETCH_ASSOC);
    return $weekfetch['lms_week_deadline'];
}

function get_current_week_picks()
{
    global $mypdo;
    $picksql = "SELECT lms_pick_game_id, lms_pick_player_id, lms_pick_match_id, lms_match_result FROM v_lms_player_picks WHERE lms_match_weekno = :matchwk";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $pickquery->execute();
    $pickfetch = $pickquery->fetchAll(PDO::FETCH_ASSOC);
    return $pickfetch;
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

function get_game_player_pick_count($gameid, $playerid)
{
    global $mypdo;
    $picksql = "SELECT lms_team_name, lms_match_date  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
    $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $pickquery->execute();
    $pickcount = $pickquery->rowCount();
    return $pickcount;
}

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

function get_still_active_game_players($gameid)
{
    global $mypdo;
    $gamesql = "SELECT * FROM v_lms_player_games WHERE lms_game_id = :gameid and lms_game_player_status = 1 ";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $gamequery->execute();
    $gamelist = $gamequery->fetchAll(PDO::FETCH_ASSOC);
    return $gamelist;
}

function get_week_state($weekno)
{
    global $mypdo;
    $weekstate = 0;
    $lookupsql = "SELECT lms_week_state FROM lms_week WHERE lms_week_no = :weekno LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':weekno' => $weekno
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $weekstate = $lookup['lms_week_state'];
    }
    return $weekstate;
}

function notify_loser($playerid, $gameid)
{
    sendemailusingtemplate('teamlose', $playerid, $gameid, '', true);
}

function notify_postponed($playerid, $gameid)
{
    sendemailusingtemplate('postponed', $playerid, $gameid, '', true);
}

function notify_winner($playerid, $gameid)
{
    sendemailusingtemplate('teamwin', $playerid, $gameid, '', true);
}

function set_game_complete($gameid)
{
    global $mypdo;
    $updgamesql = "UPDATE lms_game SET lms_game_status = 3 WHERE lms_game_id = :gameid";
    $updgamequery = $mypdo->prepare($updgamesql);
    $updgamequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $updgamequery->execute();
    $upCount = $updgamequery->rowCount();
    return $upCount;
}

function set_game_player_out($gameid, $playerid)
{
    global $mypdo;
    $upsql = "UPDATE lms_game_player SET lms_game_player_status = 2 WHERE lms_game_id = :gameid and lms_player_id = :playerid";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $upquery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $upquery->execute();
    $upCount = $upquery->rowCount();
    if ($upCount > 0) {
        $game = get_game($gameid);
        $stillActive = max(0, $game['lms_game_still_active'] - 1);
        $updgamesql = "UPDATE lms_game SET lms_game_still_active = :stillactive WHERE lms_game_id = :gameid";
        $updgamequery = $mypdo->prepare($updgamesql);
        $updgamequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
        $updgamequery->bindParam(':stillactive', $stillActive, PDO::PARAM_INT);
        $updgamequery->execute();
        $upCount = $updgamequery->rowCount();
    }
}

function set_game_week_count($gameid, $newweekcount)
{
    global $mypdo;
    $updgamesql = "UPDATE lms_game SET lms_game_week_count = :weekcount WHERE lms_game_id = :gameid";
    $updgamequery = $mypdo->prepare($updgamesql);
    $updgamequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $updgamequery->bindParam(':weekcount', $newweekcount, PDO::PARAM_INT);
    $updgamequery->execute();
    $upCount = $updgamequery->rowCount();
    return $upCount;
}

function set_pick_wl($gameid, $playerid, $matchid, $wl)
{
    global $mypdo;
    $updgamesql = "UPDATE lms_pick SET lms_pick_wl = :wl WHERE lms_pick_game_id = :gameid and lms_pick_player_id = :playerid and lms_pick_match_id = :matchid";
    $updgamequery = $mypdo->prepare($updgamesql);
    $updgamequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $updgamequery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $updgamequery->bindParam(':matchid', $matchid, PDO::PARAM_INT);
    $updgamequery->bindParam(':wl', $wl);
    $updgamequery->execute();
    $upCount = $updgamequery->rowCount();
    return $upCount;
}

function set_week_state($weekid, $newstate)
{
    global $mypdo;
    $upsql = "UPDATE lms_week SET lms_week_state = :newstate WHERE lms_week_no = :weekid";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':weekid', $weekid);
    $upquery->bindParam(':newstate', $newstate, PDO::PARAM_INT);
    $upquery->execute();
    $upcount = $upquery->rowCount();
    return $upcount;
}







?>