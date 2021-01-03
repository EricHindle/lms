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
                    if (save_match($hometeam, $matchdate, $logfile) == false) {
                        $thiserror = "** Unable to insert match : " . $hometeam . " " . date_format(date_create($matchdate), 'd-m-Y') . "\n";
                        fwrite($logfile, $thiserror);
                        $errormsg = $errormsg . $thiserror;
                    }
                    if (save_match($awayteam, $matchdate, $logfile) == false) {
                        $thiserror = "** Unable to insert match : " . $awayteam . " " . date_format(date_create($matchdate), 'd-m-Y') . "\n";
                        fwrite($logfile, $thiserror);
                        $errormsg = $errormsg . $thiserror;
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
        $matchdate = date_format(date_create($mch['lms_match_date']), 'd-m-Y');
        $matchid = $mch['lms_match_id'];
        $found = in_array($teamabbr . $matchdate, $matchlist);
        if ($found == false) {
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
$logfile = fopen($myPath . "logs/lml-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Fixtures Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d") . "\n");

$url = "https://www.thesportsman.com/football/competitions/england/premier-league/fixtures";
$search = ".fixture-list-contain";

scraping_generic($url, $search, $logfile);

fclose($logfile);
?>