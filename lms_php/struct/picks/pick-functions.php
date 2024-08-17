<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';

function delete_pick($playerid, $gameid, $matchid)
{
    global $mypdo;
    $delsql = "DELETE FROM lms_pick WHERE lms_pick_player_id=:player and lms_pick_game_id=:game and lms_pick_match_id=:match";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":player", $playerid, PDO::PARAM_INT);
    $delquery->bindParam(":game", $gameid, PDO::PARAM_INT);
    $delquery->bindParam(":match", $matchid, PDO::PARAM_INT);
    $delquery->execute();
    $delcount = $delquery->rowCount();
    $isdeleted = ($delcount > 0) ? true : false;
    return $isdeleted;
}

function decrement_available_team($playerid, $gameid, $teamid) {
    $pick = get_available_pick($playerid, $gameid, $teamid);
    if ($pick) {
        $count = $pick['lms_available_picks_count'] - 1;
        if ($count < 1) {
            delete_available_team($playerid, $gameid, $teamid);
        } else {
            update_pick_count($playerid, $gameid, $teamid, $count);     
        }
    }
}

function delete_available_team($playerid, $gameid, $teamid)
{
    global $mypdo;
    $delsql = "DELETE FROM lms_available_picks WHERE lms_available_picks_player_id=:player and lms_available_picks_game=:game and lms_available_picks_team=:team";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":player", $playerid, PDO::PARAM_INT);
    $delquery->bindParam(":game", $gameid, PDO::PARAM_INT);
    $delquery->bindParam(":team", $teamid, PDO::PARAM_INT);
    $delquery->execute();
    $delcount = $delquery->rowCount();
    $isdeleted = ($delcount > 0) ? true : false;
    return $isdeleted;
}

function increment_available_team($playerid, $gameid, $teamid)
{
    $pick = get_available_pick($playerid, $gameid, $teamid);
    if ($pick) {
        $count = $pick['lms_available_picks_count'] + 1;
        update_pick_count($playerid, $gameid, $teamid, $count);
    } else {
        insert_available_team($playerid, $gameid, $teamid, 1);
    }
}

function insert_available_team($playerid, $gameid, $teamid, $pickcount)
{
    global $mypdo;
    $inssql = "INSERT INTO lms_available_picks (lms_available_picks_game, lms_available_picks_player_id, lms_available_picks_team, lms_available_picks_count) VALUES (:gameid, :playerid, :teamid, :pickcount)";
    $insquery = $mypdo->prepare($inssql);
    $insquery->bindParam(":gameid", $gameid, PDO::PARAM_INT);
    $insquery->bindParam(":playerid", $playerid, PDO::PARAM_INT);
    $insquery->bindParam(":teamid", $teamid, PDO::PARAM_INT);
    $insquery->bindParam(":pickcount", $pickcount, PDO::PARAM_INT);
    $insquery->execute();
    $inscount = $insquery->rowCount();
    $isinserted = ($inscount > 0) ? true : false;
    return $isinserted;
}

function update_pick_count($playerid, $gameid, $teamid, $count) {
    global $mypdo;
    $updsql = "UPDATE lms_available_picks SET lms_available_picks_count = :count WHERE lms_available_picks_player_id = :playerid AND lms_available_picks_game = :gameid AND lms_available_picks_team = :teamid;";
    $updquery = $mypdo->prepare($updsql);
    $updquery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $updquery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $updquery->bindParam(':teamid', $teamid, PDO::PARAM_INT);
    $updquery->bindParam(':count', $count, PDO::PARAM_INT);    
    $updquery->execute();
}

function insert_pick($playerid, $gameid, $matchid)
{
    global $mypdo;
    $inssql = "INSERT INTO lms_pick (lms_pick_player_id, lms_pick_game_id, lms_pick_match_id, lms_pick_wl) VALUES (:player, :game, :match, '');";
    $insquery = $mypdo->prepare($inssql);
    $insquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $insquery->bindParam(':game', $gameid, PDO::PARAM_INT);
    $insquery->bindParam(':match', $matchid, PDO::PARAM_INT);
    $insquery->execute();
    $inscount = $insquery->rowCount();
    $isinserted = ($inscount > 0) ? true : false;
    return $isinserted;
}

function get_current_player_pick($gameid, $playerid)
{
    global $mypdo;
    $picksql = "SELECT lms_team_name, lms_match_date  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
    $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $pickquery->execute();
    $pickfetch = $pickquery->fetch(PDO::FETCH_ASSOC);
    return $pickfetch;
}

function get_next_player_pick($gameid, $playerid)
{
    global $mypdo;
    $picksql = "SELECT lms_team_name, lms_match_date  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
    $pickquery->bindParam(':matchwk', $_SESSION['selectweekkey']);
    $pickquery->execute();
    $pickfetch = $pickquery->fetch(PDO::FETCH_ASSOC);
    return $pickfetch;
}

function get_current_week_picks()
{
    global $mypdo;
    $picksql = "SELECT * FROM v_lms_player_picks WHERE lms_match_weekno = :matchwk";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $pickquery->execute();
    $pickfetch = $pickquery->fetchAll(PDO::FETCH_ASSOC);
    return $pickfetch;
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

function get_available_pick( $playerid, $gameid, $teamid){
    global $mypdo;
    $picksql = "SELECT * FROM lms_available_picks WHERE lms_available_picks_player_id = :playerid and lms_available_picks_game = :gameid and lms_available_picks_team = :teamid";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $pickquery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $pickquery->bindParam(':teamid', $teamid, PDO::PARAM_INT);
    $pickquery->execute();
    $pickfetch = $pickquery->fetch(PDO::FETCH_ASSOC);
    return $pickfetch;
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

?>