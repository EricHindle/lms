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

                    $gamesql = "SELECT lms_game_id, lms_game_name FROM lms_game WHERE lms_game_id = :id LIMIT 1";
                    $gamequery = $mypdo->prepare($gamesql);
                    $gamequery->execute(array(
                        ':id' => $gameid
                    ));
                    $gamecount = $gamequery->rowCount();
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
                                $postrmv = $_POST["rmv-" . $rmvid];
                                if ($postrmv == "true") {
                                    $sqlrmvteamleague = "DELETE FROM lms_game_league WHERE lms_game_league_league_id = :leagueid AND lms_game_league_game_id = :gameid";
                                    $sqlrmvteamleague = $mypdo->prepare($sqlrmvteamleague);
                                    $sqlrmvteamleague->execute(array(
                                        ':gameid' => $gameid,
                                        ':leagueid' => $rmvid
                                    ));
                                    $leaguermv = $sqlrmvteamleague->rowCount();
                                }
                            }
                        }
                        $leagueadded = 0;
                        if ($addleague == 'true') {
                            $leagueId = sanitize_int($_POST['leagueid']);
                            $sqladdteamleague = "INSERT INTO lms_game_league (lms_game_league_league_id, lms_game_league_game_id) VALUES (:leagueid, :gameid)";
                            $sqladdteamleague = $mypdo->prepare($sqladdteamleague);
                            $sqladdteamleague->execute(array(
                                ':gameid' => $gameid,
                                ':leagueid' => $leagueId
                            ));
                            $leagueadded = $sqladdteamleague->rowCount();
                            if ($leagueadded > 0) {
                                $alerttext .= "League successfully added to game.";
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