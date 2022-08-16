<?php
namespace scheduled;

/*
 * HINDLEWARE
 * Copyright (C) 2021 Eric Hindle. All rights reserved.
 */
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'scheduled/week-end-functions.php';

$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['selectweek'] = get_global_value('selectweek');
/* 
 * Match week yyyyww is the current season and week
 * Select week is the week that is open for picks
 */
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
$_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $_SESSION['selectweek'];
$_SESSION['selperiod'] = $_SESSION['selectweek'] . '/' . $_SESSION['currentseason'];
$_SESSION['deadline'] = get_current_deadline_date($_SESSION['selectweekkey']);

$logfile = fopen($myPath . "logs/lml-log-" . $_SESSION['matchweek'] . ".log", "a");
fwrite($logfile, "Player Processing --------------------------------------\n");
fwrite($logfile, date("Y-m-d H:i:s") . "\n");

/*
 * Mark picks as win/lose
 * Mark players with losing pick as out
 * Mark players with no pick as out
 */
$picks = get_current_week_picks();
fwrite($logfile, "Current week picks for active players\n");
foreach ($picks as $rs) {
    /*
     * Ignore games not in-play
     */
    if($rs['lms_game_status'] != 2){
        continue;
    }
    /*
     * If the match has been resulted but pick has not been marked up
     */
    if ($rs['lms_match_result'] != '' && $rs['lms_pick_wl'] == '') {
        $gameid = $rs['lms_pick_game_id'];
        $playerid = $rs['lms_pick_player_id'];
        $screenname = $rs['lms_player_screen_name'];
        $teamname = $rs['lms_team_name'];
        $gamename = $rs['lms_game_name'];
        $matchid = $rs['lms_pick_match_id'];
        $teamid = $rs['lms_team_id'];
        $gameplayer = get_game_player($gameid, $playerid);
        if ($gameplayer['lms_game_player_status'] == 1) {
            fwrite($logfile, "Player " . strval($playerid) . " " . $screenname . " Game " . strval($gameid) . " " . $gamename . " Match " . strval($matchid) . " " . $teamname . "\n");
            $pickwl = '';
            if ($rs['lms_match_result'] == 'l' or $rs['lms_match_result'] == 'd') {
                set_game_player_out($gameid, $playerid, $_SESSION['matchweek']);
                fwrite($logfile, "Player out of game (".  $rs['lms_match_result'] .")\n");
                $pickwl = 'l';
                notify_loser($playerid, $gameid, $teamid, $matchid);
            } else {
                $pickwl = 'w';
                if ($rs['lms_match_result'] == 'p') {
                    notify_postponed($playerid, $gameid, $teamid);
                    fwrite($logfile, "Match postponed\n");
                } else {
                    if ($rs['lms_match_result'] == 'w') {
                        notify_winner($playerid, $gameid, $teamid, $matchid);
                        fwrite($logfile, "Winning pick\n");
                    } else {
                        /* no result */
                        fwrite($logfile, "Error !! Pick has unrecognised result (" . $rs['lms_match_result'] . ")\n");
                        $pickwl = '';
                    }
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
        fwrite($logfile, "Game " . strval($game['lms_game_id']) . " " . $game['lms_game_name'] . " has no matches this week\n");
        continue;
    }
    /*
     * Check for any active players who have not made a pick this week and mark them out.
     */
    $activePlayers = get_still_active_game_players($game['lms_game_id']);
    fwrite($logfile, "Active players for " . strval($game['lms_game_id']) . " " . $game['lms_game_name'] . "\n");
    foreach ($activePlayers as $activePlayer) {
        $gameid = $game['lms_game_id'];
        $playerid = $activePlayer['lms_player_id'];
        $screenname =  $activePlayer['lms_player_screen_name'];
        fwrite($logfile, "Player " . strval($playerid) . " " . $screenname . " \n");
        if (get_game_player_pick_count($gameid, $playerid) == 0) {
            set_game_player_out($gameid, $playerid, $_SESSION['matchweek']);
            fwrite($logfile, "Player out of game - no pick\n");
            notify_no_pick($playerid, $gameid);
        }
    }
}
fwrite($logfile, "Game player statuses marked up\n");

fclose($logfile);
?>