<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020-22 Eric Hindle. All rights reserved.
 */

$myPath = '/home/lastmanl/public_html/';
// $myPath = '../';

require $myPath . 'scheduled/simple_html_dom.php';
require $myPath . 'includes/functions.php';
require $myPath . 'football-api/api-functions_v2.php';
require $myPath . 'scheduled/results-functions.php';

$_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
$_SESSION['hwkey'] = get_key();
$_SESSION['hwiv'] = get_iv();

/*
 * ============ Update Teams =============
 */
function update_teams($teams, $logfile, $leagueid)
{
    global $mypdo;
    fwrite($logfile, "----- Updating teams -----\n");
    $thisleague = $leagueid;
    foreach ($teams as $team) {
        $apiid = $team->team->id;
        $teamname = $team->team->name;
        $abbr = get_team_abbreviation($teamname);
        $foundteam = get_team_by_name($teamname);
        $ct = 0;
        $teamid = - 1;
        if (! $foundteam) {
            fwrite($logfile, "Adding " . $teamname . " " . $abbr . " " . $apiid);
            $ct = insert_team($teamname, $abbr, $apiid);

            if ($ct == 1) {
                $stmt = $mypdo->query("SELECT LAST_INSERT_ID()");
                $teamid = $stmt->fetchColumn();
            }
        } else {
            $team = $foundteam[0];
            $teamid = $team['lms_team_id'];
            fwrite($logfile, "Updating " . $teamname . " " . $abbr . " " . $apiid);
            $ct = update_team($teamid, $teamname, $abbr, $apiid);
        }
        if ($ct == 1) {
            fwrite($logfile, " * OK \n");
        } else {
            fwrite($logfile, " * No action \n");
        }
        fwrite($logfile, "Removing any team league records \n");
        delete_league_team_for_team($teamid);
        fwrite($logfile, "Adding team league record ");
        $ct = insert_team_league($teamid, $thisleague);
        if ($ct == 1) {
            fwrite($logfile, " OK \n");
        } else {
            fwrite($logfile, " Failed \n");
        }
        fwrite($logfile, "Checking abbreviation records \n");
        remove_abbr_for_team($teamid, $abbr);
        $teamswithabbr = get_teams_by_abbr($abbr);
        if (count($teamswithabbr) > 0) {
            $abbr = substr($teamname, 0, 2) . "*";
        }
        fwrite($logfile, "Adding abbreviation record ");
        insert_team_abbr($teamid, $abbr);
    }
}

/*
 * =========== Main ==========
 */

global $argv;
$logfile = fopen($myPath . "logs/lml-log-apiteamupdate.log", "a");

fwrite($logfile, "Teams Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d H:i:s") . "\n");
// Get league from parameter
$urlId = 'epl';
foreach ($argv as $param) {
    if (substr($param, 0, 2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }
}

/*
 * =========== Find league record ==========
 */
$league = get_league_from_abbr($urlId);

$leagueid = $league['lms_league_id'];
$apiLeagueId = $league['lms_league_api_id'];
fwrite($logfile, "League: " . $league['lms_league_name'] . "\n");

/*
 * =========== Get teams via api ==========
 */
fwrite($logfile, "--- Getting Teams ---\n");
$teams = get_league_teams($apiLeagueId, $logfile);
fwrite($logfile, strval(count($teams)) . " teams found\n");
update_teams($teams, $logfile, $leagueid);

/*
 * =========== End ==========
 */
fclose($logfile);
?>