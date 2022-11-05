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

function update_fixtures($url, $search, $logfile, $leagueId)
{
    global $logtext;
    $errormsg = '';
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");
    $matchdate = "";
    // get fixture list block
    fwrite($logfile, "----- Match search  -----\n");
    foreach ($html->find($search) as $found) {
        // Found at least one
        $matchlist = array();
        foreach ($found->find(".fixture-list-contain-inner") as $fixturelist) {
            $logtext = '';
            // get match date block
            foreach ($fixturelist->find(".flc-comp-title") as $datetext) {
                $textdate = trim(explode("<", $datetext->innertext, 2)[0]);
                $matchdate = strtotime($textdate);
                // fwrite($logfile, "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n");
                $matchdatetext = "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n";
            }
            // get match block
            foreach ($fixturelist->find(".flc-match-item-inner") as $match) {
                $hometeam = "";
                $awayteam = "";
                foreach ($match->find(".left") as $left) {
                    $hometeam = get_team_abbreviation($left->innertext);
                }
                foreach ($match->find(".right") as $right) {
                    $awayteam = get_team_abbreviation($right->innertext);
                }
                $today = strtotime(date("d-m-Y"));
                if ($matchdate > $today) {
                    $matchlist[] = $hometeam . date('d-m-Y', $matchdate);
                    $matchlist[] = $awayteam . date('d-m-Y', $matchdate);
                    if (save_match($hometeam, $matchdate, $logfile, $awayteam, 'h') == false) {
                        $dt = new DateTime("@$matchdate");
                        $thiserror = "** Unable to insert match : " . $hometeam . " " . date_format($dt, 'd-m-Y') . "\n";
                        // fwrite($logfile, $thiserror);
                        $logtext .= $thiserror;
                        $errormsg = $errormsg . $thiserror;
                    }
                    if (save_match($awayteam, $matchdate, $logfile, $hometeam, 'a') == false) {
                        $thiserror = "** Unable to insert match : " . $awayteam . " " . date_format($dt, 'd-m-Y') . "\n";
                        // fwrite($logfile, $thiserror);
                        $logtext .= $thiserror;
                        $errormsg = $errormsg . $thiserror;
                    }
                } else {
                    $matchlist[] = $hometeam . date('d-m-Y', $matchdate);
                    $matchlist[] = $awayteam . date('d-m-Y', $matchdate);
                    // get score
                    $homescore = 0;
                    $awayscore = 0;
                    $scoretext = '';
                    foreach ($match->find(".center") as $score) {
                        foreach ($score->find(".flc-match-error") as $rscore) {
                            $homescore = 'p';
                            $awayscore = 'p';
                            $scoretext = trim(explode("<", $rscore->innertext, 2)[0]);
                        }
                    }
                    if (! is_numeric($homescore)) {
                        fwrite($logfile, $scoretext . "  " . $hometeam . " " . $homescore . " - " . $awayscore . " " . $awayteam . "\n");
                        save_result($hometeam, $matchdate, 0, "p", $logfile);
                        save_result($awayteam, $matchdate, 0, "p", $logfile);
                    }
                }
            }
            if ($logtext != '') {
                fwrite($logfile, $matchdatetext . $logtext);
            }
        }
    }
    fwrite($logfile, "----- Match update complete ------\n");

    // check for replaced fixtures
    fwrite($logfile, "----- Checking for duplicate matches -----\n");
    $matchdata = get_all_future_matches();
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
        $found = in_array($teamabbr . $matchdate, $matchlist) && $matchleague == $leagueId;
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
    fwrite($logfile, "----- Duplicate match check complete -----\n");
    if ($errormsg != '') {
        notify_error(0, 0, $errormsg);
    }
    // clean up memory
    $html->clear();
    unset($html);
    return;
}

function update_results($results, $logfile)
{

    $lastdate = strtotime("01-01-2000");
    foreach ($results as $result) {
        
        $matchdate = strtotime(substr($result->fixture->date,0,10));
        if ($matchdate != $lastdate) {
             $matchdatetext = "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n";
             fwrite($logfile,$matchdatetext);
             $lastdate = $matchdate;
        }
   
        $hometeamApiId = $result->teams->home->id;
        $awayteamApiId = $result->teams->away->id;

        $hometeam = get_team_abbr_by_api_id($hometeamApiId);
        $awayteam = get_team_abbr_by_api_id($awayteamApiId);

        $logresulttext = '';
        // get score

        $homescore = $result->goals->home;
        $awayscore = $result->goals->away;
        $logresulttext = "  " . $hometeam . " " . $homescore . " - " . $awayscore . " " . $awayteam . "\n";

        $resultupdated = false;

        if ($homescore > $awayscore) {
            $resultupdated = save_result($hometeam, $matchdate, $homescore, "w", $logfile) || $resultupdated;
            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "l", $logfile) || $resultupdated;
        }

        if ($homescore < $awayscore) {
            $resultupdated = save_result($hometeam, $matchdate, $homescore, "l", $logfile) || $resultupdated;
            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "w", $logfile) || $resultupdated;
        }

        if ($homescore == $awayscore) {
            $resultupdated = save_result($hometeam, $matchdate, $homescore, "d", $logfile) || $resultupdated;
            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "d", $logfile) || $resultupdated;
        }
        
        if ($resultupdated) {
            fwrite($logfile, $logresulttext);
        }

    }
}

function check_dup_fixtures($matchlist, $leagueId, $logfile, $errormsg)
{
    fwrite($logfile, "----- Checking for duplicate matches -----\n");
    $matchdata = get_all_future_matches();
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
        $found = in_array($teamabbr . $matchdate, $matchlist) && $matchleague == $leagueId;
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
    fwrite($logfile, "----- Duplicate match check complete -----\n");
    return $errormsg;
}

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
global $argv;
$logfile = fopen($myPath . "logs/lml-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Fixtures Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d H:i:s") . "\n");

$urlId = 'epl';

foreach ($argv as $param) {
    if (substr($param, 0, 2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }
}

$league = get_league_from_abbr($urlId);
$apiLeagueId = $league['lms_league_api_id'];

fwrite($logfile, "League: " . $league['lms_league_name'] . "\n");

$fixtures = get_league_fixtures($apiLeagueId, $logfile);

$scheduled = array();
$noresult = array();
$results = array();

split_fixtures($fixtures, $scheduled, $results, $noresult, $logfile);

update_results($results, $logfile);

fclose($logfile);
?>