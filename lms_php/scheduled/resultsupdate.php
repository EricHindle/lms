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
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");

    // get fixture list block
    foreach ($html->find($search) as $found) {
        // Found at least one

        foreach ($found->find(".fixture-list-contain-inner") as $fixturelist) {
            $matchdatelogtext = '';
            $matchdate = "";
            // get match date block
            foreach ($fixturelist->find(".flc-comp-title") as $datetext) {
                $textdate = trim(explode("<", $datetext->innertext, 2)[0]);
                $matchdate = strtotime($textdate);
                $matchdatelogtext = "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n";
            }
            // get match block
            foreach ($fixturelist->find(".flc-match-item-inner") as $match) {

                $hometeam = "";
                $awayteam = "";
                $isFulltime = false;
                foreach ($match->find(".left") as $left) {
                    foreach ($left->find(".full-width-extra-info") as $ft) {
                        if (trim($ft->innertext) == "FT" || trim($ft->innertext) == "AET") {
                            $isFulltime = true;
                        }
                    }
                    $hometeam = get_team_abbreviation($left->innertext);
                }
                if (! $isFulltime) {
                    continue;
                }
                foreach ($match->find(".right") as $right) {
                    $awayteam = get_team_abbreviation($right->innertext);
                }
                $logresulttext = '';
                // get score
                foreach ($match->find(".center") as $score) {
                    foreach ($score->find(".l-score") as $lscore) {
                        $homescore = $lscore->innertext;
                    }
                    foreach ($score->find(".r-score") as $rscore) {
                        $awayscore = $rscore->innertext;
                    }
                    foreach ($score->find(".flc-match-error") as $rscore) {
                        $homescore = 'p';
                        $awayscore = 'p';
                    }
                    $logresulttext = "  " . $hometeam . " " . $homescore . " - " . $awayscore . " " . $awayteam . "\n";
                }
                // get score if penalties
                foreach ($match->find(".flc-match-has-pens") as $pens) {
                    $pens_score = explode(" ", $pens->innertext);
                    $pos = array_search("win", $pens_score, false);
                    $result = explode("-", $pens_score[intval($pos) + 1]);
                    $homescore = $result[0];
                    $awayscore = $result[1];
                    $logresulttext = "  " . $hometeam . " " . $homescore . " - " . $awayscore . " " . $awayteam . " on penalties\n";
                }
                $resultupdated = false;
                if (! is_numeric($homescore)) {
                    $resultupdated = $resultupdated || save_result($hometeam, $matchdate, $homescore, "p", $logfile);
                    $resultupdated = $resultupdated || save_result($awayteam, $matchdate, $awayscore, "p", $logfile);
                } else {
                    if ($homescore > $awayscore) {
                        $resultupdated = $resultupdated || save_result($hometeam, $matchdate, $homescore, "w", $logfile);
                        $resultupdated = $resultupdated || save_result($awayteam, $matchdate, $awayscore, "l", $logfile);
                    }

                    if ($homescore < $awayscore) {
                        $resultupdated = $resultupdated || save_result($hometeam, $matchdate, $homescore, "l", $logfile);
                        $resultupdated = $resultupdated || save_result($awayteam, $matchdate, $awayscore, "w", $logfile);
                    }

                    if ($homescore == $awayscore) {
                        $resultupdated = $resultupdated || save_result($hometeam, $matchdate, $homescore, "d", $logfile);
                        $resultupdated = $resultupdated || save_result($awayteam, $matchdate, $awayscore, "d", $logfile);
                    }
                }
                if ($resultupdated) {
                    fwrite($logfile, $matchdatelogtext);
                    fwrite($logfile, $logresulttext);
                    $matchdatelogtext = '';
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
global $argv;
$logfile = fopen($myPath . "logs/lml-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Results Update --------------------------------------\n");
fwrite($logfile, date("Y-m-d") . "\n");

$urlId = 'prem';
$url = "https://www.thesportsman.com/football/competitions/england/premier-league/results";

foreach ($argv as $param) {
    if (substr($param, 0,2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }
}
$url = get_global_value('results url ' . $urlId );

$search = ".fixture-list-contain";

scraping_generic($url, $search, $logfile);
fwrite($logfile, "Results update complete\n");
fclose($logfile);
?>