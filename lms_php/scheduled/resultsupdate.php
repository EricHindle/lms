<?php
/* https://www.thesportsman.com/football/competitions/england/premier-league/results */

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */

/* $myPath = '/home/lastmanl/public_html/'; */
require_once 'simple_html_dom.php';

function scraping_generic($url, $search)
{
    // Didn't find it yet.
    $return = false;

    echo "reading the url: " . $url . "\n";
    // create HTML DOM
    $html = file_get_html($url);
    echo "url has been read." . "\n";

    // get article block
    foreach ($html->find($search) as $found) {
        // Found at least one.
        $return - true;

        foreach ($html->find(".fixture-list-contain-inner") as $fixturelist) {
            $matchdate = "";

            foreach ($fixturelist->find(".flc-comp-title") as $datetext) {

                $matchdate = trim(explode("<", $datetext->innertext, 2)[0]);
                echo " \n" . "found: " . $matchdate . " \n\n";
            }

            foreach ($fixturelist->find(".flc-match-item-inner") as $match) {

                $lteam = "";
                $rteam = "";
                foreach ($match->find(".left") as $left) {

                    $lteam = trim(explode("<", $left->innertext, 2)[0]);
                    echo "found: " . $lteam . " v ";
                }

                foreach ($match->find(".right") as $right) {

                    $rteam = trim(explode("<", $right->innertext, 2)[0]);
                    echo $rteam;
                }

                foreach ($match->find(".center") as $score) {

                    foreach ($found->find(".l-score") as $lscore) {
                        $score1 = $lscore->innertext;
                    }

                    foreach ($found->find(".r-score") as $rscore) {
                        $score2 = $rscore->innertext;
                    }

                    echo "  " . $score1 . " - " . $score2 . "\n";
                }

                if ($score1 > $score2) {
                    echo $lteam . " - win " . $rteam . " - lose" . "\n";
                }

                if ($score1 < $score2) {
                    echo $lteam . " - lose " . $rteam . " - win" . "\n";
                }

                if ($score1 == $score2) {
                    echo $lteam . " - draw " . $rteam . " - draw" . "\n";
                }
            }
        }
    }

    // clean up memory
    $html->clear();
    unset($html);

    return $return;
}

$url = "https://www.thesportsman.com/football/competitions/england/premier-league/results";

$search = ".fixture-list-contain";

scraping_generic($url, $search);

?>