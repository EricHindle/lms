<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020-22 Eric Hindle. All rights reserved.
 */

$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
$logtext = '';
require $myPath . 'scheduled/simple_html_dom.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/results-functions.php';
require $myPath . 'football-api/api-functions.php';

$_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
$_SESSION['hwkey'] = get_key();
$_SESSION['hwiv'] = get_iv();

/*
 * =========== Update Results ==========
 */
function update_results($results, $logfile)
{
    fwrite($logfile, "----- Updating matches with a result -----\n");
    $matchdatelogtext = "";
    $logresulttext = "";
    $lastdate = strtotime("01-01-2000");
    foreach ($results as $result) {

        $matchdate = strtotime(substr($result->fixture->date, 0, 10));
        if ($matchdate != $lastdate) {
            $matchdatelogtext = "=============== Match date: " . date('d-m-Y', $matchdate) . " ===============\n";
            $lastdate = $matchdate;
        }

        $hometeamApiId = $result->teams->home->id;
        $awayteamApiId = $result->teams->away->id;

        $hometeam = get_team_id_by_api_id($hometeamApiId);
        $awayteam = get_team_id_by_api_id($awayteamApiId);

        $logresulttext = "";

        $homescore = $result->goals->home;
        $awayscore = $result->goals->away;
        $rtcode = $result->fixture->status->short;
        switch ($rtcode) {
            case "FT":
                break;
            case "AET":
                $logresulttext .= " after extra time";
                break;
            case "PEN":
                $logresulttext .= " on penalties";
                $homescore = $result->score->penalty->home;
                $awayscore = $result->score->penalty->away;                
                break;
        }
        $logresulttext = "  " . $result->teams->home->name . " " . $homescore . " - " . $awayscore . " " . $result->teams->away->name;
        
        $logresulttext .= "\n";
        $resultupdated = false;

        if ($homescore > $awayscore) {
            $resultupdated = save_result($hometeam, $matchdate, $homescore, "w", $rtcode, $logfile) || $resultupdated;
            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "l", $rtcode, $logfile) || $resultupdated;
        }

        if ($homescore < $awayscore) {
            $resultupdated = save_result($hometeam, $matchdate, $homescore, "l", $rtcode, $logfile) || $resultupdated;
            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "w", $rtcode, $logfile) || $resultupdated;
        }

        if ($homescore == $awayscore) {
            $resultupdated = save_result($hometeam, $matchdate, $homescore, "d", $rtcode, $logfile) || $resultupdated;
            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "d", $rtcode, $logfile) || $resultupdated;
        }

        if ($resultupdated) {
            fwrite($logfile, $matchdatelogtext);
            fwrite($logfile, $logresulttext);
            $matchdatelogtext = "";
        }
    }
}

/*
 * =========== Update Matches not played ==========
 */
function update_notplayed($noresults, $logfile)
{
    global $mypdo;
    fwrite($logfile, "----- Updating matches with postponed / cancelled / abandoned -----\n");
    $matchdatelogtext = "";
    $logresulttext = "";
    $lastdate = strtotime("01-01-2000");
    foreach ($noresults as $result) {

        $matchdate = strtotime(substr($result->fixture->date, 0, 10));
        if ($matchdate != $lastdate) {
            $matchdatelogtext = "=============== Match date: " . date('d-m-Y', $matchdate) . " ===============\n";
            $lastdate = $matchdate;
        }

        $hometeamApiId = $result->teams->home->id;
        $awayteamApiId = $result->teams->away->id;

        $hometeam = get_team_id_by_api_id($hometeamApiId);
        $awayteam = get_team_id_by_api_id($awayteamApiId);
        $wl = "";
        $logresulttext = "  " . $hometeam . " - " . $awayteam;
        $rtcode = $result->fixture->status->short;
        switch ($rtcode) {
            case "PST":
                $wl = "p";
                break;
            case "CANC":
                $wl = "c";
                break;
            case "ABD":
                $wl = "a";
                break;
        }
        $resultType = get_result_type($wl, $mypdo);
        $rtdesc = 'not played';
        if ($resultType) {
            $rtdesc = $resultType['lms_result_type_desc'];
        }
        $logresulttext .= " Match " . $rtdesc . "\n";
        $resultupdated = false;
        $homescore = 0;
        $awayscore = 0;
        $resultupdated = save_result($hometeam, $matchdate, $homescore, $wl, $rtcode, $logfile) || $resultupdated;
        $resultupdated = save_result($awayteam, $matchdate, $awayscore, $wl, $rtcode, $logfile) || $resultupdated;

        if ($resultupdated) {
            fwrite($logfile, $matchdatelogtext);
            fwrite($logfile, $logresulttext);
            $matchdatelogtext = "";
        }
    }
}

/*
 * =========== Update Scheduled fixtures ==========
 */
function update_fixtures($fixtures, $logfile, $calid)
{
    global $logtext;
    fwrite($logfile, "--------------- Updating fixtures ----------------\n");
    $errormsg = '';
    $matchlist = array();
    foreach ($fixtures as $fixture) {
        $logtext = '';
        $matchdate = strtotime(substr($fixture->fixture->date, 0, 10));
        $matchdatetext = "=============== Match date: " . date('d-m-Y', $matchdate) . " ===============\n";
        $hometeamApiId = $fixture->teams->home->id;
        $awayteamApiId = $fixture->teams->away->id;

        $hometeam = get_team_id_by_api_id($hometeamApiId);
        $awayteam = get_team_id_by_api_id($awayteamApiId);
        $matchlist[] = strval($hometeam) . "|" . date('d-m-Y', $matchdate);
        $matchlist[] = strval($awayteam) . "|" . date('d-m-Y', $matchdate);

        $dt = new DateTime("@$matchdate");
        if (save_match($hometeam, $matchdate, $logfile, $awayteam, 'h', $calid) == false) {
            $thiserror = "** Unable to insert match : " . $hometeam . " " . date_format($dt, 'd-m-Y') . "\n";
            $logtext .= $thiserror;
            $errormsg .= $thiserror;
        }
        if (save_match($awayteam, $matchdate, $logfile, $hometeam, 'a', $calid) == false) {
            $thiserror = "** Unable to insert match : " . $awayteam . " " . date_format($dt, 'd-m-Y') . "\n";
            $logtext .= $thiserror;
            $errormsg .= $thiserror;
        }
        if ($logtext != '') {
            fwrite($logfile, $matchdatetext . $logtext);
        }
    }
    if ($errormsg != '') {
        notify_error(0, 0, $errormsg);
    }
    return $matchlist;
}

/*
 * =========== Check for duplicate fixtures ==========
 */
function check_dup_fixtures($matchlist, $leagueId, $logfile)
{
    $errormsg = '';
    fwrite($logfile, "----- Checking for duplicate matches -----\n");
    $matchdata = get_all_future_matches($leagueId);
    foreach ($matchdata as $mch) {
        $teamabbr = $mch['lms_team_abbr'];
        $teamid = $mch['lms_match_team'];
        $oppid = $mch['lms_match_opp'];
        $matchtime = strtotime($mch['lms_match_date']);
        $matchdate = date('d-m-Y', $matchtime);
        $sqldate = date('Y-m-d', $matchtime);
        $matchid = $mch['lms_match_id'];
        $matchweek = $mch['lms_match_weekno'];
        $matchleague = $mch['lms_match_league'];
        $found = in_array(strval($teamid) . "|" . $matchdate, $matchlist) && $matchleague == $leagueId;
        if ($found == false) {
            // match no longer taking place

            // transfer picks if team has another match with the same team that week.
            $altmatches = get_rescheduled_match($matchweek, $sqldate, $teamid, $oppid);
            if (count($altmatches) > 0) {
                $altmatch = $altmatches[0];
                $transresult = transfer_picks($matchid, $altmatch['lms_match_id']);
                if ($transresult != false) {
                    fwrite($logfile, "Transferred " . strval($transresult) . " picks \n");
                } else {
                    fwrite($logfile, "Transfer picks failed \n");
                }
            }

            fwrite($logfile, "Removing match : " . strval($matchid) . " " . $teamabbr . " " . $matchdate . "\n");
            if (delete_match($matchid) == false) {
                $thiserror = "** Unable to remove match : " . strval($matchid) . " " . $teamabbr . " " . $matchdate . "\n";
                fwrite($logfile, $thiserror);
                $errormsg = $errormsg . $thiserror;
            }
        }
    }
    if ($errormsg != '') {
        notify_error(0, 0, $errormsg);
    }
    fwrite($logfile, "----- Duplicate match check complete -----\n");
    return;
}

/*
 * =========== Main ==========
 */

global $argv;
$logfile = fopen($myPath . "logs/lml-log-apiupdate-" . date("m-d") . ".log", "a");

$matchlist = array();

fwrite($logfile, "Fixtures Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d H:i:s") . "\n");

$updFixtures = false;
$updResults = false;

// Get league from parameter
$urlId = 'epl';
foreach ($argv as $param) {
    if (substr($param, 0, 2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }

    if (substr($param, 0, 4) == '-fix') {
        fwrite($logfile, "param : " . $param . "\n");
        $updFixtures = true;
    }

    if (substr($param, 0, 4) == '-res') {
        fwrite($logfile, "param : " . $param . "\n");
        $updResults = true;
    }
}

/*
 * =========== Find league record ==========
 */
$league = get_league_from_abbr($urlId);
$calid = $league['lms_calendar_id'];
$leagueId = $league['lms_league_id'];
$apiLeagueId = $league['lms_league_api_id'];
fwrite($logfile, "League: " . $league['lms_league_name'] . "\n");

/*
 * =========== Set session for league =====
 */
set_session_from_calendar($league);

/*
 * =========== Get fixtures via api ==========
 */
fwrite($logfile, "--- Getting League Fixtures ---\n");
$fixtures = get_league_fixtures($apiLeagueId, $logfile);
fwrite($logfile, strval(count($fixtures)) . " fixtures found\n");

/*
 * =========== Update fixtures ==========
 */
fwrite($logfile, "--- Update Fixtures by status ---\n");

if ($updFixtures) {
    fwrite($logfile, "--- Scheduled matches ---\n");
    $scheduled = split_fixtures($fixtures, "scheduled");

    fwrite($logfile, strval(count($scheduled)) . " scheduled matches found\n");
    $matchlist = update_fixtures($scheduled, $logfile, $calid);
    
    check_dup_fixtures($matchlist, $leagueId, $logfile);
}

if ($updResults) {
    fwrite($logfile, "--- Played matches ---\n");
    $played = split_fixtures($fixtures, "played");

    fwrite($logfile, strval(count($played)) . " results found\n");
    update_results($played, $logfile);

    fwrite($logfile, "--- Matches not played ---\n");
    $notplayed = split_fixtures($fixtures, "not played");

    fwrite($logfile, strval(count($notplayed)) . " postponed/cancelled/abandoned matches found\n");
    update_notplayed($notplayed, $logfile);
}

/*
 * =========== End ==========
 */
fclose($logfile);
?>