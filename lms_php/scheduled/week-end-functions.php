<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020-21 Eric Hindle. All rights reserved.
 */
require '/home/lastmanl/public_html/includes/db_connect.php';
require '/home/lastmanl/public_html/scheduled/email-functions.php';

// require '../includes/db_connect.php';
// require '../scheduled/email-functions.php';
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

function markup_outcomes()
{
    global $mypdo;
    $matchweek = $_SESSION['matchweek'];
    $picksql = "SELECT * FROM lastmanl_lms.v_lms_player_picks where lms_match_weekno = :weekno order by lms_pick_game_id";
    $pickquery = $mypdo->prepare($picksql);
    $pickquery->bindParam(':weekno', $matchweek);
    $pickquery->execute();
    $picklist = $pickquery->fetchAll(PDO::FETCH_ASSOC);
    if (count($picklist) > 0) {
        $currentgame = $picklist[0]['lms_pick_game_id'];
        $winct = 0;
        $gamepicks = array();
        foreach ($picklist as $pick) {
            if ($currentgame != $pick['lms_pick_game_id']) {
                if ($winct == 1) {
                    markup_gamePlayers($gamepicks, 2);
                }
                if ($winct == 0) {
                    markup_gamePlayers($gamepicks, 1);
                }
                $winct = 0;
                $currentgame = $pick['lms_pick_game_id'];
                $gamepicks = array();
            }
            $gamepicks[] = $pick;
            if ($pick['lms_pick_wl'] == 'w') {
                $winct += 1;
            }
        }
    }
}

function markup_gamePlayers($gamepicks, $outcome)
{
    global $mypdo;
    $updgamesql = "UPDATE lms_game_player SET lms_game_player_outcome = :outcome WHERE lms_game_id = :gameId AND lms_player_id = :playerId";
    foreach ($gamepicks as $gp) {
        $updgamequery = $mypdo->prepare($updgamesql);
        $updgamequery->bindParam(':outcome', $outcome);
        $updgamequery->bindParam(':gameId', $gp['lms_game_id']);
        $updgamequery->bindParam(':playerId', $gp['lms_player_id']);
        $updgamequery->execute();
    }
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

function get_count_of_playing_teams_this_week($gameid, $matchweek)
{
    global $mypdo;
    $selectsql = "SELECT lms_match_team FROM lms_match WHERE lms_match_weekno = :matchweek AND lms_match_team IN (
                  SELECT lms_league_team_team_id FROM lms_league_team WHERE lms_league_team_league_id IN (
                  SELECT lms_game_league_league_id FROM lms_game_league WHERE lms_game_league_game_id = :gameid))";
    $selectquery = $mypdo->prepare($selectsql);
    $selectquery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $selectquery->bindParam(':matchweek', $matchweek);
    $selectquery->execute();
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
    $picksql = "SELECT * FROM v_lms_player_picks WHERE lms_match_weekno = :matchwk";
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

function get_leagues_for_game($gameid)
{
    global $mypdo;
    $leaguesql = "SELECT lms_game_league_league_id FROM lms_game_league WHERE lms_game_league_game_id = :gameid";
    $leaguequery = $mypdo->prepare($leaguesql);
    $leaguequery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $leaguequery->execute();
    $leaguelist = $leaguequery->fetch(PDO::FETCH_ASSOC);
    return $leaguelist;
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

function get_game_player($gameid, $playerid)
{
    global $mypdo;
    $playersql = "SELECT * FROM lms_game_player WHERE lms_player_id = :id and lms_game_id = :gameId LIMIT 1";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->bindParam(":id", $playerid, PDO::PARAM_INT);
    $playerquery->bindParam(":gameId", $gameid, PDO::PARAM_INT);
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

function get_team($teamid)
{
    global $mypdo;
    $teamsql = "SELECT * FROM lms_team WHERE lms_team_id = :id LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":id", $teamid, PDO::PARAM_INT);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    return $teamfetch;
}

function get_week($weekno)
{
    global $mypdo;
    $lookupsql = "SELECT * FROM lms_week WHERE lms_week_no = :weekno LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':weekno' => $weekno
    ));
    return $lookupquery->fetch(PDO::FETCH_ASSOC);
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

function notify_loser($playerid, $gameid, $teamid, $matchid)
{
    $result = get_match_result($matchid);
    sendemailusingtemplate('teamlose', $playerid, $gameid, $teamid, array(
        $result
    ), true);
}

function notify_postponed($playerid, $gameid, $teamid)
{
    sendemailusingtemplate('postponed', $playerid, $gameid, $teamid, '', true);
}

function notify_winner($playerid, $gameid, $teamid, $matchid)
{
    $result = get_match_result($matchid);
    sendemailusingtemplate('teamwin', $playerid, $gameid, $teamid, array(
        $result
    ), true);
}

function notify_no_pick($playerid, $gameid)
{
    sendemailusingtemplate('nopick', $playerid, $gameid, 0, '', true);
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

function set_game_player_out($gameid, $playerid, $elimwk)
{
    /*
     * Set player status to 2
     */
    global $mypdo;
    $upsql = "UPDATE lms_game_player SET lms_game_player_status = 2, lms_game_player_elimination_week = :elimwk WHERE lms_game_id = :gameid and lms_player_id = :playerid";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $upquery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $upquery->bindParam(':elimwk', $elimwk);
    $upquery->execute();
    $upCount = $upquery->rowCount();
    /*
     * Remove any future picks for this player/game
     */
    remove_future_picks($gameid, $playerid);
    /*
     * If player updated, reduce the number of active players on the game
     */
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

function remove_future_picks($gameid, $playerid)
/*
 * If player is out of the game, any future picks are not required
 */
{
    global $mypdo;
    $selfgsql = "SELECT * FROM lastmanl_lms.v_lms_player_picks WHERE lms_match_weekno > :matchwk and lms_pick_player_id = :playerid and lms_pick_game_id = :gameid; ";
    $selfgquery = $mypdo->prepare($selfgsql);
    $selfgquery->bindParam(':gameid', $gameid, PDO::PARAM_INT);
    $selfgquery->bindParam(':playerid', $playerid, PDO::PARAM_INT);
    $selfgquery->bindParam(':matchwk', $_SESSION['matchweek']);
    $selfgquery->execute();
    $selfglist = $selfgquery->fetchAll(PDO::FETCH_ASSOC);
    foreach ($selfglist as $selfg) {
        delete_pick($playerid, $gameid, $selfg['lms_pick_match_id']);
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

function get_match_result($matchid)
{
    global $mypdo;
    $resultssql = "SELECT * FROM v_lms_results WHERE lms_match_id = :matchid LIMIT 1;";
    $resultsquery = $mypdo->prepare($resultssql);
    $resultsquery->bindParam(":matchid", $matchid);
    $resultsquery->execute();
    $result = $resultsquery->fetch(PDO::FETCH_ASSOC);
    $ateam = $result['home_team_name'];
    $bteam = $result['away_team_name'];
    $ascore = $result['home_score'];
    $bscore = $result['away_score'];
    $resulttext = '';
    if ($result['lms_match_ha'] == 'h') {
        $resulttext = $ateam . ' ' . $ascore . ' - ' . $bscore . ' ' . $bteam;
    } else {
        $resulttext = $bteam . ' ' . $bscore . ' - ' . $ascore . ' ' . $ateam;
    }
    return $resulttext;
}

?>