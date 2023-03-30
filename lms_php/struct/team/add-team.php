<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['teamname'], $_POST['leagueid'])) {
                $teamname = sanitize_message_string($_POST['teamname']);
                $teamabbr = "";
                $teamnamearray = explode(" ", $teamname);
                if (count($teamnamearray) == 1) {
                    $teamabbr = substr($teamname, 0, 3);
                } elseif (count($teamnamearray) > 1) {
                    $teamabbr = substr($teamnamearray[0], 0, 1) . substr($teamnamearray[1], 0, 2);
                }
                $teamabbr = strtoupper($teamabbr);
                $leagueId = $_POST['leagueid'];
                if ($teamname) {
                    $html = "";
                    $cusql = "SELECT lms_team_id FROM lms_team WHERE lms_team_name = :teamname LIMIT 1";
                    $cuquery = $mypdo->prepare($cusql);
                    $cuquery->bindParam(':teamname', $teamname);
                    $cuquery->execute();
                    $cucount = $cuquery->rowCount();

                    if ($cucount > 0) {
                        $html .= "<script>
										alert('A team with that name already exists.');
										window.location.href='team-main.php';
									</script>";
                    } else {
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $sqladdteam = "INSERT INTO lms_team (lms_team_name, lms_team_active, lms_team_abbr) VALUES (:teamname, 1, :abbr)";
                        $stmtaddteam = $mypdo->prepare($sqladdteam);
                        $stmtaddteam->bindParam(":abbr", $teamabbr);
                        $stmtaddteam->bindParam(":teamname", $teamname);
                        $stmtaddteam->execute();
                        $stmt = $mypdo->query("SELECT LAST_INSERT_ID()");
                        $teamid = $stmt->fetchColumn();
                        $added = $stmtaddteam->rowCount();
                        if ($added == 1) {
                            $sqladdteamleague = "INSERT INTO lms_league_team (lms_league_team_league_id, lms_league_team_team_id) VALUES (:leagueid, :teamid)";
                            $stmtaddteamleague = $mypdo->prepare($sqladdteamleague);
                            $stmtaddteamleague->execute(array(
                                ':teamid' => $teamid,
                                ':leagueid' => $leagueId
                            ));
                            $leagueadded = $stmtaddteamleague->rowCount();
                            $sqladdteamabbr = "INSERT INTO lms_team_abbr (lms_team_abbr_team_id, lms_team_abbr_abbr) VALUES (:teamid, :teamabbr)";
                            $stmtaddteamabbr = $mypdo->prepare($sqladdteamabbr);
                            $stmtaddteamabbr->bindParam(":teamid", $teamid, PDO::PARAM_INT);
                            $stmtaddteamabbr->bindParam(":teamabbr", $teamabbr);
                            $stmtaddteamabbr->execute();
                        }

                        $html .= "<script>
											alert('" . $added . " teams added.');
											window.location.href='team-main.php';
										</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='team-main.php';
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