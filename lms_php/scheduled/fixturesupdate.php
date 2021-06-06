<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020-21 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'scheduled/simple_html_dom.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/results-functions.php';

function scraping_generic($url, $search, $logfile)
{
    $errormsg = '';
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");
    $matchdate = "";
    // get fixture list block
    foreach ($html->find($search) as $found) {
        // Found at least one
        $matchlist = array();
        foreach ($found->find(".fixture-list-contain-inner") as $fixturelist) {

            // get match date block
            foreach ($fixturelist->find(".flc-comp-title") as $datetext) {
                $textdate = trim(explode("<", $datetext->innertext, 2)[0]);
                $matchdate = strtotime($textdate);
                fwrite($logfile, "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n");
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
                    if (save_match($hometeam, $matchdate, $logfile, $awayteam) == false) {
                        $thiserror = "** Unable to insert match : " . $hometeam . " " . date_format(date_create($matchdate), 'd-m-Y') . "\n";
                        fwrite($logfile, $thiserror);
                        $errormsg = $errormsg . $thiserror;
                    }
                    if (save_match($awayteam, $matchdate, $logfile, $hometeam) == false) {
                        $thiserror = "** Unable to insert match : " . $awayteam . " " . date_format(date_create($matchdate), 'd-m-Y') . "\n";
                        fwrite($logfile, $thiserror);
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
        }
    }

    // check for replaced fixtures
    fwrite($logfile, "----- Checking for duplicate matches -----\n");
    $matchdata = get_all_future_matches();
    foreach ($matchdata as $mch) {
        $teamabbr = $mch['lms_team_abbr'];
        $teamid = $mch['lms_match_team'];
        $oppid = $mch['lms_match_opp'];        
        $matchdate = date_format(date_create($mch['lms_match_date']), 'd-m-Y');
        $sqldate = date_format(date_create($mch['lms_match_date']), 'Y-m-d');
        $matchid = $mch['lms_match_id'];
        $matchweek = $mch['lms_match_weekno'];
        $found = in_array($teamabbr . $matchdate, $matchlist);
        if ($found == false) {
            // match no longer taking place
            
            // transfer picks if team has another match with the same team that week.
            $altmatches = get_rescheduled_match($matchweek, $sqldate, $teamid, $oppid);
            if (count($altmatches) > 0){
                $altmatch = $altmatches[0];
                $transresult = transfer_picks($matchid,$altmatch['lms_match_id']);
                if ($transresult != false){
                    fwrite($logfile, "Transferred " . strval($transresult) . " picks \n");
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
    if ($errormsg != ''){
          notify_error(0, 0, $errormsg);
    }
    // clean up memory
    $html->clear();
    unset($html);
    return;
}

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
global $argv;
$logfile = fopen($myPath . "logs/lml-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Fixtures Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d") . "\n");

$urlId = 'prem';
$url = "https://www.thesportsman.com/football/competitions/england/premier-league/fixtures";

foreach ($argv as $param) {
    if (substr($param, 0,2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }
}
$url = get_global_value('fixtures url ' . $urlId );

$search = ".fixture-list-contain";

scraping_generic($url, $search, $logfile);

fclose($logfile);
?>