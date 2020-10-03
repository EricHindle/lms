<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'scheduled/week-end-functions.php';
/*require $myPath . 'struct/match/match-functions.php';
require $myPath . 'struct/player/player-functions.php';
require $myPath . 'struct/picks/pick-functions.php';
require $myPath . 'struct/game/game-functions.php'; */

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['selectweek'] = get_global_value('selectweek');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
$_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $_SESSION['selectweek'];
$_SESSION['selperiod']=   $_SESSION['selectweek'] . '/' .$_SESSION['currentseason'] ;
$_SESSION['deadline'] = get_current_deadline_date($_SESSION['selectweekkey']);   



$weekstate = get_week_state($_SESSION['matchweek']);

$logfile = fopen("lml-log-".$_SESSION['matchweek'].".log","a");

/*
 * Confirming results
 */
$missingresultct = get_count_of_matches_with_no_result();
if ($missingresultct > 0) {

} else {

    if ($weekstate <= 1) {
        $weekstate = 1;
        set_week_state($_SESSION['matchweek'], $weekstate);

        /*
         * Mark picks as win/lose
         * Mark players with losing pick as out
         * Mark players with no pick as out
         */
        $picks = get_current_week_picks();
        foreach ($picks as $rs) {
            $gameid = $rs['lms_pick_game_id'];
            $playerid = $rs['lms_pick_player_id'];
            $matchid = $rs['lms_pick_match_id'];
            $pickwl = '';
            if ($rs['lms_match_result'] == 'l' or $rs['lms_match_result'] == 'd') {
                set_game_player_out($gameid, $playerid);
                $pickwl = 'l';
                notify_loser($playerid, $gameid);
            } else {
                $pickwl = 'w';
                if ($rs['lms_match_result'] == 'p') {
                    notify_postponed($playerid, $gameid);
                } else {
                    notify_winner($playerid, $gameid);
                }
            }
            set_pick_wl($gameid, $playerid, $matchid, $pickwl);
        }
        $activeGames = get_active_games();
        foreach ($activeGames as $game) {
            $activePlayers = get_still_active_game_players($game['lms_game_id']);
            foreach ($activePlayers as $activePlayer) {
                $gameid = $game['lms_game_id'];
                $playerid = $activePlayer['lms_player_id'];
                if (get_game_player_pick_count($gameid, $playerid) == 0) {
                    set_game_player_out($gameid, $playerid);
                    notify_no_pick($playerid, $gameid);
                }
            }
        }
    }
    fwrite($logfile,"Game player statuses marked up\n");
    if ($weekstate <= 2) {
        $weekstate = 2;
        set_week_state($_SESSION['matchweek'], $weekstate);
        /*
         * Increment game week number and mark completed games (no remaining players)
         */
        $activegames = get_active_games();
        foreach ($activegames as $game) {
            if ($game['lms_game_still_active'] == 0) {
                set_game_complete($game['lms_game_id']);
            } else {
                $gameweekcount = $game['lms_game_week_count'] + 1;
                set_game_week_count($game['lms_game_id'], $gameweekcount);
            }
        }
        set_week_state($_SESSION['matchweek'], 3);
    }
    fwrite($logfile,"Incremented game week number\n");
    if ($weekstate <= 3) {
        $weekstate = 3;
        set_week_state($_SESSION['matchweek'], $weekstate);
        /*
         * Marking up starting games as in-play
         */
        $nextWeek = $_SESSION['currentweek'] + 1;
        $nextmatchweek = $_SESSION['currentseason'] . sprintf('%02d', $nextWeek);
        activateGames($nextmatchweek);
        set_week_state($_SESSION['matchweek'], 4);
    }
    fwrite($logfile,"Marked up starting games as in-play\n");
    if ($weekstate <= 4) {
        $weekstate = 4;
        set_week_state($_SESSION['matchweek'], $weekstate);
        /*
         * Rolling week forward
         */
        $nextWeek = $_SESSION['currentweek'] + 1;
        set_global_value('currweek', sprintf('%02d', $nextWeek));
        set_week_state($_SESSION['matchweek'], 5);
        $_SESSION['currentweek'] = get_global_value('currweek');
        $_SESSION['currentseason'] = get_global_value('currseason');
        $_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
    }
    fclose();
}

?>