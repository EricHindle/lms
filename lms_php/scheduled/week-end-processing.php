<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/week-end-functions.php';

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['selectweek'] = get_global_value('selectweek');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
$_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $_SESSION['selectweek'];
$_SESSION['selperiod'] = $_SESSION['selectweek'] . '/' . $_SESSION['currentseason'];
$_SESSION['deadline'] = get_current_deadline_date($_SESSION['selectweekkey']);

$weekstate = get_week_state($_SESSION['matchweek']);

$logfile = fopen($myPath . "logs/lml-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Weekend Processing --------------------------------------\n");
fwrite($logfile, date("Y-m-d") . "\n");
if (check_start_date() == 1) {

    fwrite($logfile, "Weekend processing for match week " . $_SESSION['matchweek'] . "\n");

    /*
     * Confirm that all results have been entered
     */

    $missingresultct = get_count_of_matches_with_no_result();

    if ($missingresultct > 0) {
        fwrite($logfile, strval($missingresultct) . " matches have not been resulted\n");
    } else {
        /*
         * All results entered
         */
        if ($weekstate <= 1) {

            $weekstate = 1;
            set_week_state($_SESSION['matchweek'], $weekstate);
            fwrite($logfile, "Week state 1\n");
            /*
             * Mark picks as win/lose
             * Mark players with losing pick as out
             * Mark players with no pick as out
             */
            $picks = get_current_week_picks();
            fwrite($logfile, "Current week picks\n");
            foreach ($picks as $rs) {
                $gameid = $rs['lms_pick_game_id'];
                $playerid = $rs['lms_pick_player_id'];
                $matchid = $rs['lms_pick_match_id'];
                $gameplayer = get_game_player($gameid, $playerid);
                fwrite($logfile, "Player " . strval($playerid) . " Game " . strval($gameid) . " Match " . strval($matchid) . "\n");
                if ($gameplayer['lms_game_player_status'] == 1) {
                    fwrite($logfile, "Player still active in this game\n");
                    $pickwl = '';
                    if ($rs['lms_match_result'] == 'l' or $rs['lms_match_result'] == 'd') {
                        set_game_player_out($gameid, $playerid);
                        fwrite($logfile, "Player out of game\n");
                        $pickwl = 'l';
                        notify_loser($playerid, $gameid);
                    } else {
                        $pickwl = 'w';
                        if ($rs['lms_match_result'] == 'p') {
                            notify_postponed($playerid, $gameid);
                            fwrite($logfile, "Match postponed\n");
                        } else {
                            notify_winner($playerid, $gameid);
                            fwrite($logfile, "Winning pick\n");
                        }
                    }
                    set_pick_wl($gameid, $playerid, $matchid, $pickwl);
                    fwrite($logfile, "Pick result set\n");
                } else {
                    fwrite($logfile, "Player no longer active in this game - removing pick\n");
                    delete_pick($playerid, $gameid, $matchid);
                }
            }
            $activeGames = get_active_games();
            fwrite($logfile, "Active games\n");
            foreach ($activeGames as $game) {
                $activePlayers = get_still_active_game_players($game['lms_game_id']);
                fwrite($logfile, "Active players for " . strval($game['lms_game_id']) . "\n");
                foreach ($activePlayers as $activePlayer) {
                    $gameid = $game['lms_game_id'];
                    $playerid = $activePlayer['lms_player_id'];
                    fwrite($logfile, "Player " . strval($playerid) . " \n");
                    if (get_game_player_pick_count($gameid, $playerid) == 0) {
                        set_game_player_out($gameid, $playerid);
                        fwrite($logfile, "Player out of game - no pick\n");
                        notify_no_pick($playerid, $gameid);
                    }
                }
            }
        }
        fwrite($logfile, "Game player statuses marked up\n");
        if ($weekstate <= 2) {

            $weekstate = 2;
            set_week_state($_SESSION['matchweek'], $weekstate);
            fwrite($logfile, "Week state 2\n");
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
            set_week_state($_SESSION['matchweek'], 3);
        }
        fwrite($logfile, "Incremented game week number\n");
        if ($weekstate <= 3) {
            $weekstate = 3;
            set_week_state($_SESSION['matchweek'], $weekstate);
            fwrite($logfile, "Week state 3\n");

            /*
             * Marking up starting games as in-play
             */
            $nextWeek = $_SESSION['currentweek'] + 1;
            $nextmatchweek = $_SESSION['currentseason'] . sprintf('%02d', $nextWeek);
            activateGames($nextmatchweek);
            fwrite($logfile, "Activated games starting this week (" . $nextmatchweek . ")\n");
            set_week_state($_SESSION['matchweek'], 4);
        }
        fwrite($logfile, "Marked up starting games as in-play\n");
        if ($weekstate <= 4) {

            $weekstate = 4;
            set_week_state($_SESSION['matchweek'], $weekstate);
            fwrite($logfile, "Week state 4\n");
            /*
             * Rolling week forward
             */
            $nextWeek = $_SESSION['currentweek'] + 1;
            set_global_value('currweek', sprintf('%02d', $nextWeek));
            fwrite($logfile, "Updated match week\n");

            $newSelectWeek = $_SESSION['selectweek'] + 1;
            set_global_value('selectweek', sprintf('%02d', $newSelectWeek));
            fwrite($logfile, "Updated selection week\n");

            set_week_state($_SESSION['matchweek'], 5);
            fwrite($logfile, "Week state 5\n");
        }
    }
} else {
    fwrite($logfile, "Today is not a start date\n");
}
fclose($logfile);
?>