<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'includes/db_connect.php';

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
    return strtoupper($teamabbr);
}

function save_result($teamabbr, $matchdate, $score, $wl, $logfile)
{
    // get team id
    $teamId = get_teamId_from_abbr($teamabbr);
    if ($teamId > 0) {
        // get result (team/matchdate)
        $result = get_result($teamId, $matchdate);
        $isResultUpdated = false;
        if (! $result) {
            insert_result($teamId, $matchdate, $score, $wl);
            fwrite($logfile, "Inserted result as " . $wl . " with " . $score . " goals on " . date('d-m-Y', $matchdate) . " for " . $teamabbr . "\n");
            $isResultUpdated = true;
        } else {
            $oldscore = $result['lms_match_team_score'];
            $oldwl = $result['lms_match_team_wl'];
            if ($score != $oldscore || $wl != $oldwl) {
                update_result($teamId, $matchdate, $score, $wl);
                fwrite($logfile, "Updated result as " . $wl . " with " . $score . " goals on " . date('d-m-Y', $matchdate) . " for " . $teamabbr . "\n");
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
                fwrite($logfile, "Updated match as " . $wl . " for " . $teamabbr . " on " . date('d-m-Y', $matchdate) . "\n");
            } else {
                fwrite($logfile, "** Match not found for " . $teamabbr . " on " . date('d-m-Y', $matchdate) . "\n");
            }
        }
    } else {
        fwrite($logfile, "** No team found for " . $teamabbr . "\n");
    }
    return;
}

function save_match($teamabbr, $matchdate, $logfile)
{
    $wkno = get_match_week($matchdate);
    // get team id
    $teamId = get_teamId_from_abbr($teamabbr);
    if ($teamId > 0) {
        // get league
        $leagueId = get_leagueId($teamId);
        // get match (team/matchdate)
        $matchId = get_matchId($teamId, $matchdate);
        // insert match
        if ($matchId > 0) {
        //    fwrite($logfile, "Match exists for " . $teamabbr . " on " . date('d-m-Y', $matchdate) . "\n");
        } else {
        //    fwrite($logfile, "** Match not found for " . $teamabbr . " on " . date('d-m-Y', $matchdate) . "\n");
            insert_match($teamId, $matchdate, $wkno, '', $leagueId);
            fwrite($logfile, "** Match inserted for " . $teamabbr . " on " . date('d-m-Y', $matchdate) . "\n");
        }
    } else {
        fwrite($logfile, "** No team found for " . $teamabbr . "\n");
    }
    return;
}

function get_teamId_from_abbr($teamabbr)
{
    $teamId = - 1;
    global $mypdo;
    $teamsql = "SELECT * FROM lms_team WHERE lms_team_abbr = :abbr LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":abbr", $teamabbr);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    if ($teamquery->rowCount() == 1) {
        $teamId = $teamfetch['lms_team_id'];
    }
    return $teamId;
}

function get_all_future_matches()
{
    $today = date("Y-m-d");
    global $mypdo;
    $matchsql = "SELECT lms_match_id,lms_match_team, lms_match_date, lms_team_abbr FROM v_lms_fixture WHERE lms_match_date > :today";
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(":today", $today);
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
    $matchquery->execute();
    return;
}

function get_result($teamId, $matchdate)
{
    global $mypdo;
    $resultsql = "SELECT * FROM lms_results WHERE lms_match_date = :matchdate and lms_match_team = :teamId LIMIT 1";
    $resultquery = $mypdo->prepare($resultsql);
    $resultquery->bindParam(":teamId", $teamId);
    $resultquery->bindParam(":matchdate", date('Y-m-d', $matchdate));
    $resultquery->execute();
    $resultfetch = $resultquery->fetch(PDO::FETCH_ASSOC);
    return $resultfetch;
}

function insert_result($teamId, $matchdate, $score, $wl)
{
    global $mypdo;
    $insertresult = "INSERT INTO lms_results (lms_match_date,lms_match_team,lms_match_team_score,lms_match_team_wl) VALUES (:matchdate,:teamId,:score,:wl)";
    $stmtaddweek = $mypdo->prepare($insertresult);
    $stmtaddweek->bindParam(':matchdate', date("Y-m-d", $matchdate));
    $stmtaddweek->bindParam(':teamId', $teamId, PDO::PARAM_INT);
    $stmtaddweek->bindParam(':score', $score, PDO::PARAM_INT);
    $stmtaddweek->bindParam(':wl', $wl);
    $stmtaddweek->execute();
    return;
}

function update_result($teamId, $matchdate, $score, $wl)
{
    global $mypdo;
    $updresultsql = "UPDATE lms_results SET lms_match_team_score = :score, lms_match_team_wl = :wl WHERE lms_match_date = :matchdate AND lms_match_team = :teamId";
    $updresultquery = $mypdo->prepare($updresultsql);
    $updresultquery->bindParam(':matchdate', date("Y-m-d", $matchdate));
    $updresultquery->bindParam(':teamId', $teamId, PDO::PARAM_INT);
    $updresultquery->bindParam(':score', $score, PDO::PARAM_INT);
    $updresultquery->bindParam(':wl', $wl);
    $updresultquery->execute();
    return;
}

function get_matchId($teamId, $matchdate)
{
    $matchId = - 1;
    global $mypdo;
    $matchsql = "SELECT * FROM lms_match WHERE lms_match_date = :matchdate and lms_match_team = :teamId LIMIT 1";
    $matchquery = $mypdo->prepare($matchsql);
    $matchquery->bindParam(":teamId", $teamId);
    $matchquery->bindParam(":matchdate", date('Y-m-d', $matchdate));
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

function insert_match($teamId, $matchdate, $wkno, $wl, $league)
{
    global $mypdo;
    $insertresult = "INSERT INTO lms_match (lms_match_weekno, lms_match_team, lms_match_date, lms_match_result, lms_match_league) VALUES (:weekno, :teamId, :matchdate, :wl, :league)";
    $insertquery = $mypdo->prepare($insertresult);
    $insertquery->bindParam(':weekno', $wkno);
    $insertquery->bindParam(':matchdate', date("Y-m-d", $matchdate));
    $insertquery->bindParam(':teamId', $teamId, PDO::PARAM_INT);
    $insertquery->bindParam(':league', $league, PDO::PARAM_INT);
    $insertquery->bindParam(':wl', $wl);
    $insertquery->execute();
    return;
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

function get_match_week($matchdate)
{
    $matchweek = "";
    global $mypdo;
    $weeksql = "SELECT * FROM lastmanl_lms.lms_week WHERE lms_week_start <= :matchdate order by lms_week_no asc";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":matchdate", date("Y-m-d", $matchdate));
    $weekquery->execute();
    $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
    $lastweek = $weekfetch[$weekquery->rowCount() - 1];
    $matchweek = $lastweek['lms_week_no'];
    return $matchweek;
}
?>