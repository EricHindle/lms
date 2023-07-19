<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020-22 Eric Hindle. All rights reserved.
 */

$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'scheduled/simple_html_dom.php';
require $myPath . 'includes/functions.php';
require $myPath . 'football-api/api-functions.php';
require $myPath . 'scheduled/results-functions.php';
$_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
$_SESSION['hwkey'] = get_key();
$_SESSION['hwiv'] = get_iv();

/*
 * =========== Update League Teams ==========
 */
function update_teams($teams, $leagueid, $logfile)
{
    global $logtext;
    fwrite($logfile, "--------------- Deleting teams ----------------\n");
    delete_league_team_for_league($leagueid);
    fwrite($logfile, "Done\n");
    fwrite($logfile, "--------------- Inserting teams ----------------\n");  
    foreach ($teams as $team) {
        $logtext = '';
        $apiteamid = $team->team->id;
        $teamid = get_team_id_by_api_id($apiteamid);
        if ($teamid == -1) {
            fwrite($logfile, "---- New Team \n"); 
            fwrite($logfile, "Id:" . $team->team->id . "\n");
            fwrite($logfile, "Name: " . $team->team->name . "\n");
            fwrite($logfile, "Code: " . $team->team->code . "\n");
            insert_team($team->team->name, $team->team->code, $apiteamid);
            $teamid = get_team_id_by_api_id($apiteamid);
            fwrite($logfile, "---- Inserted team " . strval($teamid)  . "\n");
            insert_team_abbr($teamid, $team->team->code);
         } 
        
        fwrite($logfile, "Inserting league team " . strval($teamid)  . "\n");
        insert_team_league($teamid, $leagueid);
        
    }
    return;
}

/*
 * =========== Main ==========
 */

global $argv;
$logfile = fopen($myPath . "logs/lml-log-apiupdate-teams-" . date("m-d") . ".log", "a");

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
$leagueId = $league['lms_league_id'];
$apiLeagueId = $league['lms_league_api_id'];
fwrite($logfile, "League: " . $league['lms_league_name'] . "\n");

/*
 * =========== Get fixtures via api ==========
 */
fwrite($logfile, "--- Getting League Teams ---\n");
$teams = get_league_teams($apiLeagueId, $logfile);
fwrite($logfile, strval(count($teams)) . " teams found\n");

/*
 * =========== Update teams ==========
 */
fwrite($logfile, "--- Update teams ---\n");
update_teams($teams,$leagueId ,$logfile);


/*
 * =========== End ==========
 */
fclose($logfile);
?>