<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';

function delete_pick($playerid, $gameid, $matchid)
{
    global $mypdo;
    $isdeleted = false;
    $delsql = "DELETE FROM lms_pick WHERE lms_pick_player_id=:player and lms_pick_game_id=:game and lms_pick_match_id=:match";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":player", $playerid, PDO::PARAM_INT);
    $delquery->bindParam(":game", $gameid, PDO::PARAM_INT);
    $delquery->bindParam(":match", $matchid, PDO::PARAM_INT);
    $delquery->execute();
    $delcount = $delquery->rowCount();
    if ($delcount == 1) {
        $isdeleted = true;
    }
    return $isdeleted;
}

function delete_available_team($playerid, $gameid, $teamid)
{
    global $mypdo;
    $isdeleted = false;
    $delsql = "DELETE FROM lms_available_picks WHERE lms_available_picks_player_id=:player and lms_available_picks_game=:game and lms_available_picks_team=:team";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":player", $playerid, PDO::PARAM_INT);
    $delquery->bindParam(":game", $gameid, PDO::PARAM_INT);
    $delquery->bindParam(":team", $teamid, PDO::PARAM_INT);
    $delquery->execute();
    $delcount = $delquery->rowCount();
    if ($delcount == 1) {
        $isdeleted = true;
    }
    return $isdeleted;
}

function insert_available_team($playerid, $gameid, $teamid)
{
    global $mypdo;
    $isinserted = false;
    $inssql = "INSERT INTO lms_available_picks (lms_available_picks_game, lms_available_picks_player_id, lms_available_picks_team) VALUES (:gameid, :playerid, :teamid)";
    $insquery = $mypdo->prepare($inssql);
    $insquery->bindParam(":gameid", $gameid, PDO::PARAM_INT);
    $insquery->bindParam(":playerid", $playerid, PDO::PARAM_INT);
    $insquery->bindParam(":teamid", $teamid, PDO::PARAM_INT);
    $insquery->execute();
    $inscount = $insquery->rowCount();
    if ($inscount == 1) {
        $isinserted = true;
    }
    return $isinserted;
}

function insert_pick($playerid, $gameid, $matchid)
{
    global $mypdo;
    $isinserted = false;
    $inssql = "INSERT INTO lms_pick (lms_pick_player_id, lms_pick_game_id, lms_pick_match_id, lms_pick_wl) VALUES (:player, :game, :match, '');";
    $insquery = $mypdo->prepare($inssql);
    $insquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $insquery->bindParam(':game', $gameid, PDO::PARAM_INT);
    $insquery->bindParam(':match', $matchid, PDO::PARAM_INT);
    $insquery->execute();
    $inscount = $insquery->rowCount();
    if ($inscount > 0) {
        $isinserted = true;
    }
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

function get_current_week_picks() {
    global $mypdo;
    $picksql = "SELECT lms_pick_game_id, lms_pick_player_id FROM v_lms_player_picks WHERE lms_match_weekno = :matchwk";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $pickquery->execute();
    $pickfetch = $pickquery->fetchAll(PDO::FETCH_ASSOC);
    return $pickfetch;
}

function get_game_player_pick_count($gameid, $playerid) {
    global $mypdo;
    $picksql = "SELECT lms_team_name, lms_match_date  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
    $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $pickquery->execute();
    $pickcount = $pickquery->rowCount();
}

function set_pick_wl($gameid, $playerid, $matchid, $wl) {
    global $mypdo;
    $updgamesql = "UPDATE lms_pick SET lms_pick_wl = :wl WHERE lms_pick_game_id = :gameid, lms_pick_player_id = :playerid, lms_pick_match_id = :matchid";
    $updgamequery = $mypdo->prepare($updgamesql);
    $updgamequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $updgamequery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $updgamequery->bindParam(':matchid', $matchid, PDO::PARAM_INT);
    $updgamequery->bindParam(':wl', $wl);
    $updgamequery->execute();
    $upCount =  $updgamequery->rowCount();
    return $upCount;
    
}


?>