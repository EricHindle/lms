<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require_once $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/game/game-functions.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['id'], $_POST['gamename'], $_POST['gamestartweek'])) {
                $gameid = sanitize_int($_POST['id']);
                $gamename = $_POST['gamename'];
                $gamestartweek = $_POST['gamestartweek'];
                $addleague = (isset($_POST['addleague']) ? $_POST['addleague'] : "false");
                $iscancel = (isset($_POST['iscancel']) ? $_POST['iscancel'] : "false");
                if ($gameid && $gamename) {
                    $html = "";
                    $gamecount = check_game_exists($gameid);
                    $alerttext = "";
                    if ($gamecount > 0) {
                        $upsql = "";
                        if ($iscancel == "true") {
                            $upsql = "UPDATE lms_game SET lms_game_name = :gamename, lms_game_start_wkno = :startwk, lms_game_status = 4 WHERE lms_game_id = :id";
                        } else {
                            $upsql = "UPDATE lms_game SET lms_game_name = :gamename, lms_game_start_wkno = :startwk WHERE lms_game_id = :id";
                        }
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $gameid, PDO::PARAM_INT);
                        $upquery->bindParam(':gamename', $gamename);
                        $upquery->bindParam(':startwk', $gamestartweek);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
                            $alerttext = "Details updated successfully. ";
                        }
                        $leaguesql = "SELECT lms_league_name, lms_league_id FROM lms_league";
                        $leaguequery = $mypdo->prepare($leaguesql);
                        $leaguequery->execute();
                        $leaguecount = $leaguequery->rowCount();
                        if ($leaguecount > 0) {
                            $leaguefetch = $leaguequery->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($leaguefetch as $rs) {
                                $rmvid = $rs['lms_league_id'];
                                if (array_key_exists("rmv-" . $rmvid, $_POST)) {
                                    $sqlrmvteamleague = "DELETE FROM lms_game_league WHERE lms_game_league_league_id = :leagueid AND lms_game_league_game_id = :gameid";
                                    $sqlrmvteamleague = $mypdo->prepare($sqlrmvteamleague);
                                    $sqlrmvteamleague->bindParam(':gameid', $gameid, PDO::PARAM_INT);
                                    $sqlrmvteamleague->bindParam(':leagueid', $rmvid, PDO::PARAM_INT);
                                    $sqlrmvteamleague->execute();
                                    $leaguermv = $sqlrmvteamleague->rowCount();
                                    if ($leaguermv > 0) {
                                        $alerttext .= "League removed from game.";
                                    }
                                    
                                    /*
                                     * Get all teams in the league
                                     * Remove all available picks for game/team
                                     */
                                    $teamlist = get_active_teams_for_league($rmvid);
                                    foreach ($teamlist as $team) {
                                        remove_available_picks_for_team_game($gameid, $team['lms_team_id']);
                                    }
                                }
                            }
                        }
                        $leagueadded = 0;
                        if ($addleague == 'true') {
                            $leagueId = sanitize_int($_POST['leagueid']);
                            $sqladdteamleague = "INSERT INTO lms_game_league (lms_game_league_league_id, lms_game_league_game_id) VALUES (:leagueid, :gameid)";
                            $sqladdteamleague = $mypdo->prepare($sqladdteamleague);
                            $sqladdteamleague->bindParam(':gameid', $gameid, PDO::PARAM_INT);
                            $sqladdteamleague->bindParam(':leagueid', $leagueId, PDO::PARAM_INT);
                            $sqladdteamleague->execute();
                            $leagueadded = $sqladdteamleague->rowCount();
                            if ($leagueadded > 0) {
                                $alerttext .= "League added to game.";
                            }
                            
                            /*
                             * Get all players in the game
                             * Get all teams in the league
                             * Add all teams to all players available picks for game
                             */
                            $playerlist = get_players_for_game($gameid);
                            $teamlist = get_active_teams_for_league($leagueId);
                            foreach($playerlist as $player) {
                                foreach($teamlist as $team) {
                                    insert_available_team($player['lms_player_id'], $gameid, $team['lms_team_id']);
                                }
                            }
                        }
                        
                        if ($upcount > 0 || $leagueadded > 0) {
                            if ($iscancel == "true") {
                                sendcancelemailsforgame($gameid);
                            }
                            $html .= "<script>
										alert('".$alerttext."');
										window.location.href='game-manage.php';
									</script>";
                        } else {
                            $html .= "<script>
										alert('Details not altered.');
										window.location.href='game-manage.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='game-manage.php';
									</script>";
                    }
                    echo $html;
                } else {
                    echo "<script>
								alert('There was a problem. Please check details and try again.');
								window.location.href='game-manage.php';
							</script>";
                }
            } else {
                header('Location: ' . $myPath . 'index.php?error=1');
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>