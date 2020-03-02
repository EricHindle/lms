<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require_once $myPath . 'struct/picks/pick-functions.php';

date_default_timezone_set('Europe/London');

function add_player_to_game($gameid, $playerid)
{
    global $mypdo;
    $sqljoingame = "INSERT INTO lms_game_player (lms_game_id, lms_player_id, lms_game_player_status) VALUES (:gameid, :playerid, 1)";
    $stmtjoingame = $mypdo->prepare($sqljoingame);
    $stmtjoingame->bindParam(":gameid", $gameid, PDO::PARAM_INT);
    $stmtjoingame->bindParam(":playerid", $playerid, PDO::PARAM_INT);
    $stmtjoingame->execute();
    $joincount = $stmtjoingame->rowCount();
    $joinok = false;
    if ($joincount > 0) {
        $joinok = true;
        $gamesql = "SELECT lms_game_id, lms_game_total_players, lms_game_still_active FROM lms_game WHERE lms_game_id = :id";
        $gamequery = $mypdo->prepare($gamesql);
        $gamequery->execute(array(
            ':id' => $gameid
        ));
        $gamecount = $gamequery->rowCount();

        if ($gamecount > 0) {
            $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
            $total = $gamefetch['lms_game_total_players'] + 1;
            $active = $gamefetch['lms_game_still_active'] + 1;
            $upsql = "UPDATE lms_game SET lms_game_total_players = :total, lms_game_still_active = :active WHERE lms_game_id = :id";
            $upquery = $mypdo->prepare($upsql);
            $upquery->bindParam(':id', $gameid, PDO::PARAM_INT);
            $upquery->bindParam(':total', $total, PDO::PARAM_INT);
            $upquery->bindParam(':active', $active, PDO::PARAM_INT);
            $upquery->execute();
        }

        $teamsql = "SELECT lms_team_id, lms_team_name FROM lms_team WHERE lms_team_active = 1 ORDER BY lms_team_name ASC";
        $teamquery = $mypdo->prepare($teamsql);
        $teamquery->execute();
        $teamfetch = $teamquery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($teamfetch as $rs) {
            insert_available_team($playerid, $gameid, $rs['lms_team_id']);
        }
    }
    return $joinok;
}

function remove_player_from_game($gameid, $playerid)
{
    global $mypdo;
    $leaveok = false;
    $sqlleavegame = "UPDATE lms_game_player SET lms_game_player_status = 3 WHERE lms_game_id = :gameid and lms_player_id = :playerid";
    $stmtleavegame = $mypdo->prepare($sqlleavegame);
    $stmtleavegame->bindParam(":gameid", $gameid, PDO::PARAM_INT);
    $stmtleavegame->bindParam(":playerid", $playerid, PDO::PARAM_INT);
    $stmtleavegame->execute();
    $leavecount = $stmtleavegame->rowCount();
    if ($leavecount > 0) {
        $leaveok = true;
        $gamesql = "SELECT lms_game_id, lms_game_total_players, lms_game_still_active FROM lms_game WHERE lms_game_id = :id";
        $gamequery = $mypdo->prepare($gamesql);
        $gamequery->execute(array(
            ':id' => $gameid
        ));
        $gamecount = $gamequery->rowCount();

        if ($gamecount > 0) {

            $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
            $active = $gamefetch['lms_game_still_active'] - 1;
            $upsql = "UPDATE lms_game SET lms_game_still_active = :active WHERE lms_game_id = :id";
            $upquery = $mypdo->prepare($upsql);
            $upquery->bindParam(':id', $gameid, PDO::PARAM_INT);
            $upquery->bindParam(':active', $active, PDO::PARAM_INT);
            $upquery->execute();
        }
    }
    return $leaveok;
}

function generate_game_code()
{
    $allchars = "abcdefghjkmnpqrstuvwxyz23456789";
    $randstr = str_shuffle($allchars);
    $gamecode = "";
    $gamecount = - 1;
    do {
        for ($i = 1; $i < 7; $i ++) {
            $gamecode .= substr($randstr, 0, 1);
            $randstr = str_shuffle($randstr);
        }
        $gamequery = find_game_by_code($gamecode);
        $gamecount = $gamequery->rowCount();
    } while ($gamecount != 0);

    return $gamecode;
}

function find_game_by_code($gamecode)
{
    global $mypdo;
    $gamesql = "SELECT lms_game_id, lms_game_name, lms_game_manager, lms_game_status, lms_player_screen_name, lms_game_start_wkno FROM v_lms_game WHERE lms_game_code = :id";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->execute(array(
        ':id' => $gamecode
    ));

    return $gamequery;
}

function get_player_games()
{
    global $mypdo;
    $player = $_SESSION['user_id'];
    $gamesql = "SELECT * FROM v_lms_player_games WHERE lms_player_id = :player ORDER BY lms_game_player_status, lms_game_name ASC";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(':player', $player, PDO::PARAM_INT);
    $gamequery->execute();
    $gamelist = $gamequery->fetchAll(PDO::FETCH_ASSOC);
    return $gamelist;
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

function get_active_games()
{
    global $mypdo;
    $gamesql = "SELECT * FROM lms_game WHERE lms_game_status = 2";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->execute();
    $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
    return $gamefetch;
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

?>