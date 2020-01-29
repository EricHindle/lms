<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/week/week-functions.php';
require $myPath . 'struct/match/match-functions.php';
require $myPath . 'struct/player/player-functions.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access == 999) {
    $weekstate = get_week_state($_SESSION['matchweek']);
    $html = "";
    $key = $formKey->outputKey();
    echo '
				<!doctype html>
				<html>
					<head>

					    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
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
								</div>
								<div class = "row">';

    $html .= '			   <div class="well col-md-6 col-md-offset-1 textDark">';

    if ($weekstate > 0) {
        $html .= ' <div class = "row">Confirmed : all matches have been resulted</div>';
        if ($weekstate > 1) {
            $html .= '   <div class = "row">Game player statuses marked up</div>';
            if ($weekstate > 2) {
                $html .= '    <div class = "row">Winners identified</div>';
                if ($weekstate > 3) {
                    $html .= '        <div class = "row">Game statuses marked up</div>';
                    if ($weekstate > 4) {
                        $html .= '            <div class = "row">Week rolled forward</div>';
                        $html .= ' 		<div class="row">
													<br>
													<div class="col-xs-6">
														<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">OK</a>
														<br>
													</div>
												</div> ';
                    } else {
                        /*
                         * Rolling week forward
                         */
                        $nextWeek = $_SESSION['currentweek'] + 1;
                        set_global_value('currweek', sprintf('%02d', $nextWeek));
                        set_week_state($_SESSION['matchweek'], 5);
                        $_SESSION['currentweek'] = get_global_value('currweek');
                        $_SESSION['currentseason'] = get_global_value('currseason');
                        $_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
                        header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
                    }
                } else {
                    /*
                     * Marking up starting games as in-play
                     */
                    $nextWeek = $_SESSION['currentweek'] + 1;
                    $nextmatchweek = $_SESSION['currentseason'] . sprintf('%02d', $nextWeek);
                    activateGames($nextmatchweek);
                    set_week_state($_SESSION['matchweek'], 4);
                    header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
                }
            } else {
                /*
                 * Increment game week number and mark completed games (no remaining players)
                 */
                $activegames = get_active_games();
                foreach ($activegames as $game) {
                    if ($game['lms_game_still_active'] == 0) {
                        set_game_complete($game['lms_game_id']);
                    } else {
                        $gameweekcount = $game['lms_game_week_count'] + 1;
                        set_game_weekcount($game['lms_game_id'], $gameweekcount);
                    }
                }
                set_week_state($_SESSION['matchweek'], 3);
                header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
            }
        } else {
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
                    notify_winner($playerid, $gameid);
                }
                set_pick_wl($gameid, $playerid, $matchid, $pickwl);
                set_game_player_out($gameid, $playerid);
            }
            $activeGames = get_active_games();
            foreach ($activeGames as $game) {
                $activePlayers = get_still_actve_game_players($game['lms_game_id']);
                foreach ($activePlayers as $activePlayer) {
                    $gameid = $game['lms_game_id'];
                    $playerid = $activePlayer['lms_player_id'];

                    if (get_game_player_pick_count($gameid, $playerid) == 0) {
                        set_game_player_out($gameid, $playerid);
                        notify_no_pick($playerid, $gameid);
                    }
                }
            }
            set_week_state($_SESSION['matchweek'], 2);
            header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
        }
    } else {
        /*
         * Confirming results
         */
        $missingresultct = get_count_of_matches_with_no_result();
        if ($missingresultct > 0) {
            $html .= "<script>
						alert('Not all matches have been resulted. Enter results and try again.');
						window.location.href='weekend-admin.php';
					</script>";
        } else {
            set_week_state($_SESSION['matchweek'], 1);
            header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
        }
    }

    $html .= '			   </div>
								</div>
                            </div>
                        </section>
            		</body>
				</html>';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}

?>