<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020-22 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'scheduled/simple_html_dom.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/results-functions.php';

$_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
$_SESSION['hwkey'] = get_key();
$_SESSION['hwiv'] = get_iv();

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
                    $resultupdated = save_result($hometeam, $matchdate, $homescore, "p", $logfile) || $resultupdated;
                    $resultupdated = save_result($awayteam, $matchdate, $awayscore, "p", $logfile) || $resultupdated;
                } else {
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

function scraping_sky($url, $search, $logfile)
{
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");
    
    // get fixture list block
    foreach ($html->find($search) as $found) {
        // Found at least one
        $matchdatelogtext = '';
        $matchdate = "";
        $monthyear = "";
       
        foreach ($found->find("div.fixres__body") as $fixresbody) {
            $tags = $fixresbody->find("*");
            foreach ($tags as $tag) {
                
                if ($tag->tag == 'h3'){
                    $monthyear = explode(" ", trim( $tag->innertext ));
                    fwrite($logfile,"----- Month/year  " . $monthyear[0] . "/" . $monthyear[1] . "------\n");
                }
                if ($tag->tag == 'h4') {
                    $matchdate = strtotime(trim( $tag->innertext ). " " . $monthyear[1]);
                    $matchdatelogtext = "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n";
                    fwrite($logfile, $matchdatelogtext);
                }
                if ($tag->tag == 'a' ) {
                    $hometeam = "";
                    $awayteam = "";
                    $homescore = "";
                    $awayscore = "";
                    $matchtime = "";
                    $logresulttext = '';
                    $isFulltime = false;
                    $ishometeam = true;
//                    fwrite($logfile,"============== Match =============" . "\n");
                    $attrs = $tag->attr;
                    foreach ($attrs as $attrkey => $attrvalue) {
                        if ($attrkey == 'data-status') {
                            if (trim($attrvalue) == "FT" || trim($attrvalue) == "AET") {
                                $isFulltime = true;
                            }
//                            fwrite($logfile,"status = " . trim( $attrvalue ) . "\n");
                        }
                    }
                    $matchtags = $tag->find("*");
                    foreach ($matchtags as $matchtag) {
                        if ($matchtag->tag == 'span' ) {
                            $attrs = $matchtag->attr;
                            foreach ($attrs as $attrkey => $attrvalue) {
                                if ($attrkey == 'title'){
//                                    fwrite($logfile, "team = " . $attrvalue . "\n");
                                }
                                if ($attrvalue == 'swap-text__target') {
//                                    fwrite($logfile,"Team full = " . trim( $matchtag->innertext ) . "\n");
                                    if ($ishometeam == true){
                                        $hometeam = get_team_abbreviation(trim( $matchtag->innertext ));
                                    } else {
                                        $awayteam = get_team_abbreviation(trim( $matchtag->innertext ));
                                    }
                                }
                                if ($attrvalue == 'matches__teamscores-side') {
//                                    fwrite($logfile,"Score = " . trim( $matchtag->innertext ) . "\n");
                                    if ($ishometeam) {
                                        $homescore = trim( $matchtag->innertext );
                                        $ishometeam = false;
                                    } else {
                                        $awayscore = trim( $matchtag->innertext );
                                    }
                                }
                                if ($attrvalue == 'matches__date') {
//                                    fwrite($logfile,"KO time = " . trim( $matchtag->innertext ) . "\n");
                                    $matchtime =  trim( $matchtag->innertext );
                                }
                            }
                        }
                    }
                    $resultupdated = false;
                    if($hometeam != "") {
                        if (! is_numeric($homescore)) {
                            $resultupdated = save_result($hometeam, $matchdate, $homescore, "p", $logfile) || $resultupdated;
                            $resultupdated = save_result($awayteam, $matchdate, $awayscore, "p", $logfile) || $resultupdated;
                        } else {
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
                        }
                        $logresulttext = "  " . $hometeam . " " . $homescore . " - " . $awayscore . " " . $awayteam . "\n";
                        fwrite($logfile,$logresulttext);
                    }
//                    fwrite($logfile,"============== End Match =============" . "\n");
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
fwrite($logfile, date("Y-m-d H:i:s") . "\n");

$urlId = 'epl';
$url = "https://www.skysports.com/premier-league-results";

foreach ($argv as $param) {
    if (substr($param, 0, 2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }
}
$source = get_global_value('results_source');
$url = get_global_value($source . ' results url ' . $urlId);
$league = get_league_from_abbr($urlId);

fwrite($logfile,"League: " . $league['lms_league_name'] . "\n");

$search = "div.fixres";

if ($source == 'sky') {
    scraping_sky($url, $search, $logfile);
} else {
    scraping_generic($url, $search, $logfile);
}

fwrite($logfile, "Results update complete\n");
fclose($logfile);
?>