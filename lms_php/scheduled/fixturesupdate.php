<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
$myPath = "../";
require $myPath . 'scheduled/simple_html_dom.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/results-functions.php';

function scraping_generic($url, $search, $logfile)
{
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");

    // get fixture list block
    foreach ($html->find($search) as $found) {
        // Found at least one

        foreach ($found->find(".fixture-list-contain-inner") as $fixturelist) {
            $matchdate = "";
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
                    save_match($hometeam, $matchdate, $logfile);
                    save_match($awayteam, $matchdate, $logfile);
                }
            }
        }
    }

    // clean up memory
    $html->clear();
    unset($html);

    return;
}

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
$logfile = fopen($myPath . "logs/test-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Fixtures Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d") . "\n");

$url = "https://www.thesportsman.com/football/competitions/england/premier-league/fixtures";
$search = ".fixture-list-contain";

scraping_generic($url, $search, $logfile);

fclose($logfile);
?>