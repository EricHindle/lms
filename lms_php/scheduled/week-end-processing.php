<?php
namespace scheduled;

/*
 * HINDLEWARE
 * Copyright (C) 2020-21 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/week-end-functions.php';

$startingweeks = get_starting_weeks();
foreach ($startingweeks as $newweek) {
    $msg = '';
    
    $calid = $newweek['lms_week_calendar'];
    $calrow = get_calendar_row($calid);

    $calyear = $calrow['lms_calendar_season'];
    $calweek = $calrow['lms_calendar_current_week'];
    $wkyear = $newweek['lms_year'];
    $wkweek = $newweek['lms_week'];

    $_SESSION['calendar'] = $calid;
    $_SESSION['currentseason'] = $calrow['lms_calendar_season'];
    $_SESSION['currentweek'] = $calrow['lms_calendar_current_week'];
    $_SESSION['selectweek'] = $calrow['lms_calendar_select_week'];
    $currentweek = sprintf('%02d', $_SESSION['currentweek']);
    $selectweek = sprintf('%02d', $_SESSION['selectweek']);
    $_SESSION['matchweek'] = $_SESSION['currentseason'] . $currentweek;
    $_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $selectweek;
    $_SESSION['selperiod'] = $selectweek . '/' . $_SESSION['currentseason'];

    $logfile = fopen($myPath . "logs/lml-log-cal" . strval($calid) . "-wk" . $_SESSION['matchweek'] . ".log", "a");
    fwrite($logfile, "Weekend Processing --------------------------------------\n");
    fwrite($logfile, date("Y-m-d H:i:s") . "\n");

    $week = get_week_row($_SESSION['currentseason'],$currentweek, $calid);

    if ($wkyear > $calyear || ($wkyear == $calyear && $wkweek > $calweek)) {

        if ($week) {

            $_SESSION['deadline'] = $week['lms_week_deadline'];

            $_SESSION['encrypted'] = filter_var(get_global_value('encrypt'), FILTER_VALIDATE_BOOLEAN);
            $_SESSION['hwkey'] = get_key();
            $_SESSION['hwiv'] = get_iv();

            $weekstate = $week['lms_week_state'];

            $timenow = strtotime("now");
            $timeweekend = strtotime($week['lms_week_end']);

            fwrite($logfile, "Weekend processing for match week " . $_SESSION['matchweek'] . "\n");
            fwrite($logfile, "Week state " . strval($weekstate) . "\n");
            /*
             * Confirm that all results have been entered
             */

            $missingresultct = get_count_of_matches_with_no_result();

            if ($missingresultct > 0) {
                fwrite($logfile, "Warning !! Some matches (" . strval($missingresultct) . ") have not been resulted\n");
            }

            if ($weekstate <= 1) {

                $weekstate = 1;
                set_week_state($_SESSION['matchweek'], $calid, $weekstate);
                fwrite($logfile, "Week state 1\n");
                /*
                 * Mark picks as win/lose
                 * Mark players with losing pick as out
                 * Mark players with no pick as out
                 */
                $picks = get_current_week_picks();
                fwrite($logfile, "Current week picks\n");
                foreach ($picks as $rs) {
                    /*
                     * Ignore games not in-play
                     */
                    if ($rs['lms_game_status'] != 2) {
                        continue;
                    }
                    /*
                     * If the match has been resulted but pick has not been marked up
                     */
                    if ($rs['lms_match_result'] != '' && $rs['lms_pick_wl'] == '') {
                        $gameid = $rs['lms_pick_game_id'];
                        $playerid = $rs['lms_pick_player_id'];
                        $matchid = $rs['lms_pick_match_id'];
                        $teamid = $rs['lms_team_id'];
                        $gameplayer = get_game_player($gameid, $playerid);
                        fwrite($logfile, "Player " . strval($playerid) . " Game " . strval($gameid) . " Match " . strval($matchid) . "\n");
                        if ($gameplayer['lms_game_player_status'] == 1) {
                            fwrite($logfile, "Player still active in this game\n");
                            $result_type = get_result_type($rs['lms_match_result'], $mypdo);
                            $pickwl = '';
                            if ($result_type) {
                                $pickwl = $result_type['lms_result_type_wl'];
                            }

                            if ($pickwl == '') {
                                /* no result */
                                fwrite($logfile, "Error !! Pick has unrecognised result (" . $rs['lms_match_result'] . ")\n");
                            } elseif ($pickwl == 'l') {
                                set_game_player_out($gameid, $playerid, $_SESSION['matchweek']);
                                fwrite($logfile, "Player out of game (" . $rs['lms_match_result'] . ")\n");
                                notify_loser($playerid, $gameid, $teamid, $matchid);
                            } elseif ($pickwl == 'w') {
                                if ($rs['lms_match_result'] == 'w') {
                                    notify_winner($playerid, $gameid, $teamid, $matchid);
                                    fwrite($logfile, "Winning pick\n");
                                } else {
                                    notify_postponed($playerid, $gameid, $teamid, $result_type['lms_result_type_desc']);
                                    fwrite($logfile, "Match " . $result_type['lms_result_type_desc'] . "\n");
                                }
                            }
                            set_pick_wl($gameid, $playerid, $matchid, $pickwl);
                            fwrite($logfile, "Pick result set\n");
                        }
                    }
                }
                $activeGames = get_active_games();
                fwrite($logfile, "Active games\n");
                foreach ($activeGames as $game) {
                    /*
                     * Check if this game has no matches this week.
                     */
                    $playingteams = get_count_of_playing_teams_this_week($game['lms_game_id'], $_SESSION['matchweek']);

                    if ($playingteams == 0) {
                        fwrite($logfile, "Game " . strval($game['lms_game_id']) . " has no matches this week\n");
                        continue;
                    }
                    $activePlayers = get_still_active_game_players($game['lms_game_id']);
                    fwrite($logfile, "Active players for " . strval($game['lms_game_id']) . "\n");
                    foreach ($activePlayers as $activePlayer) {
                        $gameid = $game['lms_game_id'];
                        $playerid = $activePlayer['lms_player_id'];
                        fwrite($logfile, "Player " . strval($playerid) . " \n");
                        if (get_game_player_pick_count($gameid, $playerid) == 0) {
                            set_game_player_out($gameid, $playerid, $_SESSION['matchweek']);
                            fwrite($logfile, "Player out of game - no pick\n");
                            notify_no_pick($playerid, $gameid);
                        }
                    }
                }
                fwrite($logfile, "Game player statuses marked up\n");
            } else {
                $msg = $msg . "Week state " . strval($weekstate) . "\n";
            }

            if ($weekstate <= 2) {

                $weekstate = 2;
                set_week_state($_SESSION['matchweek'], $calid, $weekstate);
                fwrite($logfile, "Week state 2\n");
                /*
                 * Get this weeks picks and mark up any outcomes
                 */
                markup_outcomes($_SESSION['matchweek']);
                /*
                 * Increment game week number and mark completed games (no remaining players)
                 */
                $activegames = get_active_games();
                fwrite($logfile, "Active games\n");
                foreach ($activegames as $game) {
                    fwrite($logfile, "Game " . strval($game['lms_game_id']) . "\n");
                    if ($game['lms_game_still_active'] == 0) {
                        set_game_complete($game['lms_game_id']);
                        fwrite($logfile, "No active players - set complete\n");
                    } else {
                        $gameweekcount = $game['lms_game_week_count'] + 1;
                        set_game_week_count($game['lms_game_id'], $gameweekcount);
                        fwrite($logfile, "Updated week count\n");
                    }
                }
                set_week_state($_SESSION['matchweek'], $calid, 3);
                fwrite($logfile, "Incremented game week number\n");
            }

            if ($weekstate <= 3) {
                $weekstate = 3;
                set_week_state($_SESSION['matchweek'], $calid, $weekstate);
                fwrite($logfile, "Week state 3\n");

                /*
                 * Marking up starting games as in-play
                 */
                $nextWeek = $_SESSION['currentweek'] + 1;
                $nextmatchweek = $_SESSION['currentseason'] . sprintf('%02d', $nextWeek);
                activateGames($nextmatchweek, $calid);
                fwrite($logfile, "Activated games starting this week (" . $nextmatchweek . ")\n");
                set_week_state($_SESSION['matchweek'], $calid, 4);
                fwrite($logfile, "Marked up starting games as in-play\n");
            }

            if ($weekstate <= 4) {
                $weekstate = 4;
                set_week_state($_SESSION['matchweek'], $calid, $weekstate);
                fwrite($logfile, "Week state 4\n");
                /*
                 * Rolling week forward
                 */
                $nextWeek = $_SESSION['currentweek'] + 1;
                set_global_value('currweek', sprintf('%02d', $nextWeek), 0);
                fwrite($logfile, "Updated global match week\n");

                $newSelectWeek = $_SESSION['selectweek'] + 1;
                set_global_value('selectweek', sprintf('%02d', $newSelectWeek), 0);
                fwrite($logfile, "Updated global selection week\n");

                update_calendar_weeks($calid, $nextWeek, $newSelectWeek);
                fwrite($logfile, "Updated calendar weeks\n");
                
                set_week_state($_SESSION['matchweek'], $calid, 5);
                fwrite($logfile, "Week state 5\n");
            }
        } else {
            fwrite($logfile, "No week record for week ending." . "\n");
        }
    } else {
        fwrite($logfile, "Week already processed." . "\n");
    }

    if (strlen($msg) > 0) {
        $msg = "Weekend processing for match week " . $_SESSION['matchweek'] . ". \n" . $msg;
        sendemailusingtemplate('weekendprocessing', 0, 0, 0, array(
            $msg
        ), false);
    }
    fclose($logfile);
}
?>