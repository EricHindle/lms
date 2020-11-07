<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */

require '/home/lastmanl/public_html/includes/db_connect.php';

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
    $insertresult = "INSERT INTO lms_results (lms_match_date,lms_match_team,lms_match_team_score,lms_match_team_wl) VALUES (:matchdate,:teamId,:score,:wl);";
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

?>