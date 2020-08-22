<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
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
            if (isset($_POST['gamename'], $_POST['gamestartweek'], $_POST['leagueid'])) {
                $gamename = $_POST['gamename'];
                $gamestartweek = $_POST['gamestartweek'];
                $leagueId = $_POST['leagueid'];
                if ($gamename) {
                    $html = "";
                    $cusql = "SELECT lms_game_id FROM lms_game WHERE lms_game_name = :gamename LIMIT 1";
                    $cuquery = $mypdo->prepare($cusql);
                    $cuquery->bindParam(':gamename', $gamename);
                    $cuquery->execute();
                    $cucount = $cuquery->rowCount();

                    if ($cucount > 0) {
                        $html .= "<script>
										alert('A game with that name already exists.');
										window.location.href='game-manage.php';
									</script>";
                    } else {
                        $playerid = $_SESSION['user_id'];
                        $gamecode = generate_game_code();
                        $sqladdgame = "INSERT INTO lms_game (lms_game_start_wkno, lms_game_name, lms_game_status, lms_game_week_count, lms_game_total_players, lms_game_still_active, lms_game_manager, lms_game_code) 
                                                    VALUES (:startwkno, :gamename, 1, 0, 0, 0, :playerid, :gamecode)";
                        $stmtaddgame = $mypdo->prepare($sqladdgame);
                        $stmtaddgame->bindParam(":startwkno", $gamestartweek);
                        $stmtaddgame->bindParam(":gamename", $gamename);
                        $stmtaddgame->bindParam(":playerid", $playerid, PDO::PARAM_INT);
                        $stmtaddgame->bindParam(":gamecode", $gamecode);
                        $stmtaddgame->execute();
                        $added = $stmtaddgame->rowCount();
                        $gameid = $mypdo->lastInsertId();
                        if ($added == 1) {
                            $sqladdgameleague = "INSERT INTO lms_game_league (lms_game_league_game_id, lms_game_league_league_id) VALUES (:gameid, :leagueid)";
                            $stmtaddgameleague = $mypdo->prepare($sqladdgameleague);
                            $stmtaddgameleague->execute(array(
                                ':gameid' => $gameid,
                                ':leagueid' => $leagueId
                            ));
                            $leagueadded = $stmtaddgameleague->rowCount();
                            add_player_to_game($gameid, $_SESSION['user_id']);
                            sendemailusingtemplate('newgame', $playerid, $gameid, '', true);
                            $html .= "<script>              
									alert('Game added.');
									window.location.href='game-manage.php';
								  </script>";
                        } else {
                            $html .= "<script>
									alert('Game was not added.');
									window.location.href='game-manage.php';
								  </script>";
                        }
                    }

                    echo $html;
                } else {
                    echo "<script>
								alert('Game name missing. Please check details and try again.');
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