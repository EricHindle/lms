<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/week/week-functions.php';
require $myPath . 'struct/match/match-functions.php';
require $myPath . 'struct/player/player-functions.php';
require $myPath . 'struct/picks/pick-functions.php';
require $myPath . 'struct/game/game-functions.php';

sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    $weekstate = get_week_state($_SESSION['matchweek']);
    $html = "";
    echo '
				<!doctype html>
				<html>
					<head>

					    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
					    <meta charset="UTF-8">

					    <title>Week End Processing</title>

					    <meta name="viewport" content="width=device-width, initial-scale=1">
					    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
					    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
					    <script src="' . $myPath . 'js/jquery.js"></script>
					    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
					</head>
					        
					<body>';
    include $myPath . 'globNAV.php';
    $html .= '
						<section id="homeSection">
					    <br><br>
					        <div class="container">
            					<div class="row">
									<div class="col-md-8">
									    <h1><strong>Week End Processing for period ' . $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'] . '</strong></h1>
									</div>
        							<div class="col-md-1">
        								<a href="' . $myPath . 'struct/week/weekend-admin.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
        							</div>
								</div>	
                            	<div class="row">
                                    <div class="col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10">';
    /*
     * Confirming results
     */
    $missingresultct = get_count_of_matches_with_no_result();
    if ($missingresultct > 0) {
        header('Location: ' . $myPath . 'struct/week/weekend-admin.php?error=1');
    } else {
        $html .= '    <div class="alert alert-success">Confirmed : all matches have been resulted</div>';

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
        $html .= '    <div class="alert alert-success">Game player statuses marked up</div>';
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
        $html .= '    <div class="alert alert-success">Incremented game week number</div>';
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
        $html .= '    <div class="alert alert-success">Marked up starting games as in-play</div>';
        if ($weekstate <= 4) {
            $weekstate = 4;
            set_week_state($_SESSION['matchweek'], $weekstate);
            /*
             * Rolling week forward
             */
            $nextWeek = $_SESSION['currentweek'] + 1;
            set_global_value('currweek', sprintf('%02d', $nextWeek),0);
            $newSelectWeek = $_SESSION['selectweek'] + 1;
            set_global_value('selectweek', sprintf('%02d', $newSelectWeek),0);
            
            
            set_week_state($_SESSION['matchweek'], 5);
            $_SESSION['currentweek'] = get_global_value('currweek');
            $_SESSION['currentseason'] = get_global_value('currseason');
            $_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
        }
        $html .= '    <div class="alert alert-success">Rolled week forward</div>';
        $html .= '     </div>
                         </section>
                         </body>
                         </html>';
        echo $html;
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}

?>