<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';

function get_player_status($code)
{
    global $mypdo;
    $text = "code missing";
    $lookupsql = "SELECT lms_game_player_status_id, lms_game_player_status_text FROM lms_game_player_status WHERE lms_game_player_status_id = :id LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':id' => $code
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $text = $lookup['lms_game_player_status_text'];
    }
    return $text;
}

function get_game_status($code)
{
    global $mypdo;
    $text = "code missing";
    $lookupsql = "SELECT lms_game_status_id, lms_game_status_text FROM lms_game_status WHERE lms_game_status_id = :id LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':id' => $code
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $text = $lookup['lms_game_status_text'];
    }
    return $text;
}

function get_game_name($code)
{
    global $mypdo;
    $text = "game missing";
    $lookupsql = "SELECT lms_game_name FROM lms_game WHERE lms_game_id = :id LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':id' => $code
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $text = $lookup['lms_game_name'];
    }
    return $text;
}

function get_game_player_status($game, $player)
{
    global $mypdo;
    $mygamessql = "SELECT lms_game_player_status, lms_game_player_status_text FROM v_lms_player_games WHERE lms_player_id = :player and lms_game_id = :game LIMIT 1";
    $mygamesquery = $mypdo->prepare($mygamessql);
    $mygamesquery->bindParam(":player", $player, PDO::PARAM_INT);
    $mygamesquery->bindParam(":game", $game, PDO::PARAM_INT);
    $mygamesquery->execute();
    $mygamesfetch = $mygamesquery->fetch(PDO::FETCH_ASSOC);
    return $mygamesfetch;
}

function get_current_game($game)
{
    global $mypdo;
    $mygamessql = "SELECT * FROM lms_game WHERE lms_game_id = :game LIMIT 1";
    $mygamesquery = $mypdo->prepare($mygamessql);
    $mygamesquery->bindParam(":game", $game, PDO::PARAM_INT);
    $mygamesquery->execute();
    $mygamesfetch = $mygamesquery->fetch(PDO::FETCH_ASSOC);
    return $mygamesfetch;
}

function get_remaining_weeks($includecurrentweek, $calendar)
{
    global $mypdo;

    $weeksql = "SELECT lms_week_no, lms_week, lms_year, lms_week_start FROM lms_week WHERE lms_week > :week and lms_year = :season and lms_week_calendar = :cal";
    if ($includecurrentweek) {
        $weeksql = "SELECT lms_week_no, lms_week, lms_year, lms_week_start FROM lms_week WHERE lms_week >= :week and lms_year = :season and lms_week_calendar = :cal";
    }
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $_SESSION['currentweek'], PDO::PARAM_INT);
    $weekquery->bindParam(":season", $_SESSION['currentseason'], PDO::PARAM_INT);
    $weekquery->bindParam(":cal", $calendar, PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
    return $weekfetch;
}

function get_deadline_date()
{
    global $mypdo;
    $weeksql = "SELECT lms_week_deadline FROM lms_week WHERE lms_week = :week and lms_year = :season LIMIT 1";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $_SESSION['selectweek'], PDO::PARAM_INT);
    $weekquery->bindParam(":season", $_SESSION['currentseason'], PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetch(PDO::FETCH_ASSOC);
    return $weekfetch['lms_week_deadline'];
}

?>