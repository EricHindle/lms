<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020-22 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'includes/db_connect.php';
require $myPath . 'scheduled/email-functions.php';

function notify_error($playerid, $gameid, $errormsg)
{
    sendemailusingtemplate('error', $playerid, $gameid, 0, array(
        $errormsg
    ), false);
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

function get_team_abbreviation($teamtext)
{
    $teamabbr = "";
    $teamname = trim(explode("<", $teamtext, 2)[0]);
    $teamnamearray = explode(" ", $teamname);
    if (count($teamnamearray) == 1) {
        $teamabbr = substr($teamname, 0, 3);
    } elseif (count($teamnamearray) > 1) {
        $teamabbr = substr($teamnamearray[0], 0, 1) . substr($teamnamearray[1], 0, 2);
    }

    global $mypdo;
    $teamsql = "SELECT * FROM v_lms_team_lookup WHERE lms_team_abbr_abbr = :abbr LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":abbr", $teamabbr);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    if ($teamquery->rowCount() == 1) {
        $teamabbr = $teamfetch['lms_team_abbr'];
    }
    return strtoupper($teamabbr);
}

function save_result($teamId, $matchdate, $score, $wl, $status, $logfile)
{
    $isResultUpdated = false;
    // get team id
    // $teamId = get_teamId_from_abbr($teamabbr);

    if ($teamId > 0) {
        $team = get_team($teamId);
        if (isset($team)) {
            // get result (team/matchdate)
            $result = get_result($teamId, $matchdate);
            $isResultUpdated = false;
            if (count($result) == 0) {
                insert_result($teamId, $matchdate, $score, $wl, $status);
                fwrite($logfile, "Inserted result as " . $wl . " with " . $score . " goals on " . date('d-m-Y', $matchdate) . " for " . $team['lms_team_name'] . "\n");
                $isResultUpdated = true;
            } else {
                $oldscore = $result['lms_match_team_score'];
                $oldwl = $result['lms_match_team_wl'];
                if ($score != $oldscore || $wl != $oldwl) {
                    update_result($teamId, $matchdate, $score, $wl, $status);
                    fwrite($logfile, "Updated result as " . $wl . " with " . $score . " goals on " . date('d-m-Y', $matchdate) . " for " . $team['lms_team_name'] . "\n");
                    $isResultUpdated = true;
                } else {
                    /* fwrite($logfile, "Already resulted " . $teamabbr . " for " . date('d-m-Y', $matchdate) . "\n"); */
                }
            }
            if ($isResultUpdated) {
                // get match (team/matchdate)
                $matchId = get_matchId($teamId, $matchdate);
                // update match with match result
                if ($matchId > 0) {
                    update_match_wl($matchId, $wl);
                    fwrite($logfile, "Updated match as " . $wl . " for " . $team['lms_team_name'] . " on " . date('d-m-Y', $matchdate) . "\n");
                } else {
                    fwrite($logfile, "** Match not found for " . $team['lms_team_name'] . " on " . date('d-m-Y', $matchdate) . "\n");
                }
            }
        } else {
            fwrite($logfile, "** No team found for " . $teamid . "\n");
        }
    }
    return $isResultUpdated;
}

function save_match($teamId, $matchdate, $logfile, $oppid, $homeaway, $calid)
{
    global $logtext;
    $mdt = date("Y-m-d", $matchdate);
    $isOK = true;
    $wkno = get_match_week($matchdate, $calid);
    // get team id
    // $teamId = get_teamId_from_abbr($teamabbr);
    // $oppid = get_teamId_from_abbr($oppabbr);
    if ($teamId > 0) {
        $team = get_team($teamId);
        $oppteam = get_team($oppid);
        if (isset($team)) {
            // get league
            $leagueId = get_leagueId($teamId);
            // get match (team/matchdate)
            $matchId = get_matchId($teamId, $matchdate);
            // insert match
            if ($matchId <= 0) {
                if (insert_match($teamId, $matchdate, $wkno, '', $leagueId, $oppid, $homeaway, $calid) == false) {
                    $isOK = false;
                } else {
                    $logtext .= "** Match inserted for " . $team['lms_team_name'] . " on " . $mdt . "\n";
                }
            }
        } else {
            $logtext .= "** No team found for " . $teamid . "\n";
            $isOK = false;
        }
    } else {
        $isOK = false;
    }
    return $isOK;
}

function get_teamId_from_abbr($teamabbr)
{
    $teamId = - 1;
    global $mypdo;
    $teamsql = "SELECT * FROM lms_team_abbr WHERE lms_team_abbr_abbr = :abbr LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":abbr", $teamabbr);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    if ($teamquery->rowCount() == 1) {
        $teamId = $teamfetch['lms_team_abbr_team_id'];
    }
    return $teamId;
}

function get_teamId_from_apiid($apiId)
{
    $teamId = - 1;
    global $mypdo;
    $teamsql = "SELECT * FROM lms_team WHERE lms_team_api_id = :apiid LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":apiid", $apiId, PDO::PARAM_INT);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    if ($teamquery->rowCount() == 1) {
        $teamId = $teamfetch['lms_team_id'];
    }
    return $teamId;
}

function get_all_future_matches($leagueId)
{
    $today = date("Y-m-d");
    global $mypdo;
    $matchsql = "SELECT lms_match_id,lms_match_team, lms_match_date, lms_team_abbr, lms_match_opp, lms_opp_abbr, lms_match_weekno, lms_match_league FROM v_lms_fixture WHERE lms_match_date > :today AND lms_match_league = :league ";
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(":today", $today);
    $matchquery->bindParam(":league", $leagueId, PDO::PARAM_INT);
    $matchquery->execute();
    $matchdata = $matchquery->fetchAll(PDO::FETCH_ASSOC);
    return $matchdata;
}

function delete_match($matchId)
{
    global $mypdo;
    $matchsql = "DELETE FROM lms_match WHERE lms_match_id = :id";
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(":id", $matchId, PDO::PARAM_INT);
    $delete_ok = $matchquery->execute();
    return $delete_ok;
}

function get_result($teamId, $matchdate)
{
    global $mypdo;
    $mdt = date("Y-m-d", $matchdate);
    $resultsql = "SELECT * FROM lms_results WHERE lms_match_date = :matchdate and lms_match_team = :teamId LIMIT 1";
    $resultquery = $mypdo->prepare($resultsql);
    $resultquery->bindParam(":teamId", $teamId);
    $resultquery->bindParam(":matchdate", $mdt);
    $resultquery->execute();
    $resultfetch = $resultquery->fetch(PDO::FETCH_ASSOC);
    if ($resultquery->rowCount() == 0) {
        $resultfetch = [];
    }
    return $resultfetch;
}

function insert_result($teamId, $matchdate, $score, $wl, $status)
{
    global $mypdo;
    $mdt = date("Y-m-d", $matchdate);
    $insertresult = "INSERT INTO lms_results (lms_match_date,lms_match_team,lms_match_team_score,lms_match_team_wl, lms_match_status) VALUES (:matchdate,:teamId,:score,:wl,:status)";
    $stmtaddweek = $mypdo->prepare($insertresult);
    $stmtaddweek->bindParam(':matchdate', $mdt);
    $stmtaddweek->bindParam(':teamId', $teamId, PDO::PARAM_INT);
    $stmtaddweek->bindParam(':score', $score, PDO::PARAM_INT);
    $stmtaddweek->bindParam(':wl', $wl);
    $stmtaddweek->bindParam(':status', $status);
    $stmtaddweek->execute();
    return;
}

function update_result($teamId, $matchdate, $score, $wl, $status)
{
    global $mypdo;
    $mdt = date("Y-m-d", $matchdate);
    $updresultsql = "UPDATE lms_results SET lms_match_team_score = :score, lms_match_team_wl = :wl, lms_match_status = :status WHERE lms_match_date = :matchdate AND lms_match_team = :teamId";
    $updresultquery = $mypdo->prepare($updresultsql);
    $updresultquery->bindParam(':matchdate', $mdt);
    $updresultquery->bindParam(':teamId', $teamId, PDO::PARAM_INT);
    $updresultquery->bindParam(':score', $score, PDO::PARAM_INT);
    $updresultquery->bindParam(':wl', $wl);
    $updresultquery->bindParam(':status', $status);
    $updresultquery->execute();
    return;
}

function get_matchId($teamId, $matchdate)
{
    $mdt = date("Y-m-d", $matchdate);
    $matchId = - 1;
    global $mypdo;
    $matchsql = "SELECT * FROM lms_match WHERE lms_match_date = :matchdate and lms_match_team = :teamId LIMIT 1";
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(":teamId", $teamId);
    $matchquery->bindParam(":matchdate", $mdt);
    $matchquery->execute();
    $matchfetch = $matchquery->fetch(PDO::FETCH_ASSOC);
    if ($matchquery->rowCount() == 1) {
        $matchId = $matchfetch['lms_match_id'];
    }
    return $matchId;
}

function update_match_wl($matchid, $matchresult)
{
    global $mypdo;
    $updmatchsql = "UPDATE lms_match SET lms_match_result = :result WHERE lms_match_id = :id";
    $updmatchquery = $mypdo->prepare($updmatchsql);
    $updmatchquery->bindParam(':id', $matchid);
    $updmatchquery->bindParam(':result', $matchresult);
    $updmatchquery->execute();
    return;
}

function insert_match($teamId, $matchdate, $wkno, $wl, $league, $oppId, $homeaway, $calid)
{
    global $mypdo;
    $mdt = date("Y-m-d", $matchdate);
    $insertresult = "INSERT INTO lms_match (lms_match_weekno, lms_match_team, lms_match_date, lms_match_result, lms_match_league, lms_match_opp, lms_match_ha, lms_match_calendar) VALUES (:weekno, :teamId, :matchdate, :wl, :league, :oppId, :homeaway, :cal)";
    $insertquery = $mypdo->prepare($insertresult);
    $insertquery->bindParam(':weekno', $wkno);
    $insertquery->bindParam(':matchdate', $mdt);
    $insertquery->bindParam(':teamId', $teamId, PDO::PARAM_INT);
    $insertquery->bindParam(':league', $league, PDO::PARAM_INT);
    $insertquery->bindParam(':oppId', $oppId, PDO::PARAM_INT);
    $insertquery->bindParam(':wl', $wl);
    $insertquery->bindParam(':homeaway', $homeaway);
    $insertquery->bindParam(':cal', $calid, PDO::PARAM_INT);
    return $insertquery->execute();
}

function get_leagueId($teamId)
{
    $leagueId = - 1;
    global $mypdo;
    $leaguesql = "SELECT * FROM lms_league_team WHERE lms_league_team_team_id = :teamId LIMIT 1";
    $leaguequery = $mypdo->prepare($leaguesql);
    $leaguequery->bindParam(":teamId", $teamId);
    $leaguequery->execute();
    $leaguefetch = $leaguequery->fetch(PDO::FETCH_ASSOC);
    if ($leaguequery->rowCount() == 1) {
        $leagueId = $leaguefetch['lms_league_team_league_id'];
    }
    return $leagueId;
}

function get_league_from_abbr($abbr)
{
    global $mypdo;
    $leaguesql = "SELECT * FROM v_lms_league_calendar WHERE lms_league_abbr =  :abbr LIMIT 1";
    $leaguequery = $mypdo->prepare($leaguesql);
    $leaguequery->bindParam(":abbr", $abbr);
    $leaguequery->execute();
    $leagueRow = $leaguequery->fetch(PDO::FETCH_ASSOC);
    return $leagueRow;
}

function get_match_week($matchdate, $calid)
{
    $mdt = date("Y-m-d", $matchdate);
    $matchweek = "";
    global $mypdo;
    $weeksql = "SELECT * FROM lastmanl_lms.lms_week WHERE lms_week_start <= :matchdate and lms_week_calendar = :cal order by lms_week_no asc";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":matchdate", $mdt);
    $weekquery->bindParam(":cal", $calid, PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
    $lastweek = $weekfetch[$weekquery->rowCount() - 1];
    $matchweek = $lastweek['lms_week_no'];
    return $matchweek;
}

function get_rescheduled_match($matchweek, $matchdate, $teamid, $oppid)
{
    global $mypdo;
    $matchsql = "SELECT * from lms_match where lms_match_weekno = :weekno and lms_match_team = :teamid and lms_match_opp = :oppid and lms_match_date != :matchdate ";
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(":weekno", $matchweek);
    $matchquery->bindParam(":teamid", $teamid, PDO::PARAM_INT);
    $matchquery->bindParam(":oppid", $oppid, PDO::PARAM_INT);
    $matchquery->bindParam(":matchdate", $matchdate);
    $matchquery->execute();
    $matchdata = $matchquery->fetchAll(PDO::FETCH_ASSOC);
    return $matchdata;
}

function transfer_picks($matchid, $altmatchid)
{
    global $mypdo;
    $transsql = "UPDATE lms_pick SET lms_pick_match_id = :altmatchid  WHERE lms_pick_match_id = :matchid";
    $transquery = $mypdo->prepare($transsql);
    $transquery->bindParam(':matchid', $matchid, PDO::PARAM_INT);
    $transquery->bindParam(':altmatchid', $altmatchid, PDO::PARAM_INT);
    return $transquery->execute();
}
?>