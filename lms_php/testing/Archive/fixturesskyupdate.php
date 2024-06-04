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

$_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
$_SESSION['hwkey'] = get_key();
$_SESSION['hwiv'] = get_iv();

function scraping_generic($url, $search, $logfile, $leagueId)
{
    global $logtext;
    $errormsg = '';
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");
    $matchdate = "";
    // get fixture list block
    fwrite($logfile,"----- Match search  -----\n");
    foreach ($html->find($search) as $found) {
        // Found at least one
        $matchlist = array();
        foreach ($found->find(".fixture-list-contain-inner") as $fixturelist) {
            $logtext = '';
            // get match date block
            foreach ($fixturelist->find(".flc-comp-title") as $datetext) {
                $textdate = trim(explode("<", $datetext->innertext, 2)[0]);
                $matchdate = strtotime($textdate);
              //  fwrite($logfile, "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n");
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
                    //    fwrite($logfile, $thiserror);
                        $logtext .= $thiserror;
                        $errormsg = $errormsg . $thiserror;
                    }
                    if (save_match($awayteam, $matchdate, $logfile, $hometeam, 'a') == false) {
                        $thiserror = "** Unable to insert match : " . $awayteam . " " . date_format($dt, 'd-m-Y') . "\n";
                    //    fwrite($logfile, $thiserror);
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
            if ($logtext != ''){
                fwrite($logfile,$matchdatetext.$logtext);
            }
            
            
        }
    }
    fwrite($logfile,"----- Match update complete ------\n");

    // check for replaced fixtures
    
    $errormsg = check_dup_fixtures($matchlist,$leagueId, $logfile, $errormsg);
    
    
    if ($errormsg != ''){
          notify_error(0, 0, $errormsg);
    }
    // clean up memory
    $html->clear();
    unset($html);
    return;
}

function scraping_sky($url, $search, $logfile, $leagueId)
{
    global $logtext;
    $errormsg = '';
    fwrite($logfile, "Reading the url: " . $url . "\n");
    // create HTML DOM
    $html = file_get_html($url);
    fwrite($logfile, "Url has been read." . "\n");
    
    // get fixture list block
    foreach ($html->find($search) as $found) {
        // Found at least one
        $matchlist = array();
        $matchdatelogtext = '';
        $matchdate = "";
        $monthyear = "";
        
        foreach ($found->find("div.fixres__body") as $fixresbody) {
            $logtext = "";
            $tags = $fixresbody->find("*");
            foreach ($tags as $tag) {
                
                if ($tag->tag == 'h3'){
                    $monthyear = explode(" ", trim( $tag->innertext ));
           //         fwrite($logfile,"----- Month/year  " . $monthyear[0] . "/" . $monthyear[1] . "------\n");
                }
                if ($tag->tag == 'h4') {
                    $matchdate = strtotime(trim( $tag->innertext ). " " . $monthyear[1]);
                    $matchdatelogtext = "----- Match date: " . date('d-m-Y', $matchdate) . " -----\n";
           //         fwrite($logfile, $matchdatelogtext);
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
                                    $teamabbr = get_team_abbreviation(trim( $matchtag->innertext ));
                                    if ($teamabbr != ""){
                                        //                                    fwrite($logfile,"Team full = " . trim( $matchtag->innertext ) . "\n");
                                        if ($ishometeam == true){
                                            $hometeam = $teamabbr;
                                        } else {
                                            $awayteam = $teamabbr;
                                        }
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
                    if ($hometeam != ""){
                        $today = strtotime(date("d-m-Y"));
                        if ($matchdate > $today) {
                            $matchlist[] = $hometeam . date('d-m-Y', $matchdate);
                            $matchlist[] = $awayteam . date('d-m-Y', $matchdate);
                            
              //              fwrite($logfile, date('d-m-Y', $matchdate) . " " . $hometeam . " v " . $awayteam . "\n");
                            
                            if (save_match($hometeam, $matchdate, $logfile, $awayteam, 'h') == false) {
                                $dt = new DateTime("@$matchdate");
                                $thiserror = "** Unable to insert match : " . $hometeam . " " . date_format($dt, 'd-m-Y') . "\n";
                            //    fwrite($logfile, $thiserror);
                                $logtext .= $thiserror;
                                $errormsg = $errormsg . $thiserror;
                            }
                            if (save_match($awayteam, $matchdate, $logfile, $hometeam, 'a') == false) {
                                $dt = new DateTime("@$matchdate");
                                $thiserror = "** Unable to insert match : " . $awayteam . " " . date_format($dt, 'd-m-Y') . "\n";
                            //    fwrite($logfile, $thiserror);
                                $logtext .= $thiserror;
                                $errormsg = $errormsg . $thiserror;
                            }
                        }
                    }
                    //                    fwrite($logfile,"============== End Match =============" . "\n");
                }
                if ($logtext != ''){
                    fwrite($logfile,$matchdatetext.$logtext);
                }
            }
        }
    }
    
    fwrite($logfile,"----- Match update complete ------\n");
    // check for replaced fixtures
    
    $errormsg = check_dup_fixtures($matchlist, $leagueId,$logfile,$errormsg);
    
    
    if ($errormsg != ''){
        notify_error(0, 0, $errormsg);
    }
    // clean up memory
    $html->clear();
    unset($html);
    
    return;
}


function check_dup_fixtures($matchlist, $leagueId,  $logfile,$errormsg) {
    fwrite($logfile, "----- Checking for duplicate matches -----\n");
    $matchdata = get_all_future_matches();
    foreach ($matchdata as $mch) {
        $teamabbr = $mch['lms_team_abbr'];
        $teamid = $mch['lms_match_team'];
        $oppid = $mch['lms_match_opp'];
        $matchtime = strtotime($mch['lms_match_date']);
        $matchdate = date('d-m-Y',$matchtime);
        $sqldate = date('Y-m-d',$matchtime);
        $matchid = $mch['lms_match_id'];
        $matchweek = $mch['lms_match_weekno'];
        $matchleague =$mch['lms_match_league'];
        $found = in_array($teamabbr . $matchdate, $matchlist) && $matchleague == $leagueId;
        if ($found == false) {
            // match no longer taking place
            
            // transfer picks if team has another match with the same team that week.
            $altmatches = get_rescheduled_match($matchweek, $sqldate, $teamid, $oppid);
            if (count($altmatches) > 0){
                $altmatch = $altmatches[0];
                $transresult = transfer_picks($matchid,$altmatch['lms_match_id']);
                if ($transresult != false){
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
$url = "https://www.thesportsman.com/football/competitions/england/premier-league/fixtures";

foreach ($argv as $param) {
    if (substr($param, 0,2) == 'u-') {
        fwrite($logfile, "param : " . $param . "\n");
        $urlId = substr($param, 2);
    }
}
$source = get_global_value('results_source');
$url = get_global_value($source . ' fixtures url ' . $urlId);

$league = get_league_from_abbr($urlId);

fwrite($logfile,"League: " . $league['lms_league_name'] . "\n");

$search = "div.fixres";

if ($source == 'sky') {
    scraping_sky($url, $search, $logfile, $league['lms_league_id']);
} else {
    scraping_generic($url, $search, $logfile, $league['lms_league_id']);
}

fclose($logfile);
?>