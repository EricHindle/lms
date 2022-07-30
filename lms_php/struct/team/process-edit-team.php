<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/team/team_functions.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['id'], $_POST['teamname'], $_POST['teamabbr'])) {
                $teamid = sanitize_int($_POST['id']);
                $teamname = $_POST['teamname'];
                $teamabbr = $_POST['teamabbr'];
                $isactive = (isset($_POST['isactive']) ? $_POST['isactive'] : "false");
                $myactive = ($isactive == "true" ? 1 : 0);

                $addleague = (isset($_POST['addleague']) ? $_POST['addleague'] : "false");

                if ($teamid && $teamname) {
                    $html = "";

                    $teamsql = "SELECT lms_team_id, lms_team_name, lms_team_active FROM lms_team WHERE lms_team_id = :id LIMIT 1";
                    $teamquery = $mypdo->prepare($teamsql);
                    $teamquery->execute(array(
                        ':id' => $teamid
                    ));
                    $teamcount = $teamquery->rowCount();
                    $alerttext = "";
                    if ($teamcount > 0) {
                        remove_abbr_for_team($teamid);
                        $abbrlist = explode(', ',$teamabbr);
                        foreach ($abbrlist as $abbr) {
                            insert_team_abbr($teamid, $abbr);
                        }
                        
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $upsql = "UPDATE lms_team SET lms_team_name = :teamname, lms_team_active = :active, lms_team_abbr = :abbr WHERE lms_team_id = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $teamid, PDO::PARAM_INT);
                        $upquery->bindParam(':teamname', $teamname);
                        $upquery->bindParam(':abbr', $abbrlist[0]);
                        $upquery->bindParam(':active', $myactive, PDO::PARAM_INT);
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
                                    $sqlrmvteamleague = "DELETE FROM lms_league_team WHERE lms_league_team_league_id = :leagueid AND lms_league_team_team_id = :teamid";
                                    $sqlrmvteamleague = $mypdo->prepare($sqlrmvteamleague);
                                    $sqlrmvteamleague->execute(array(
                                        ':teamid' => $teamid,
                                        ':leagueid' => $rmvid
                                    ));
                                    $leaguermv = $sqlrmvteamleague->rowCount();
                                }
                            }
                        }
                        $leagueadded = 0;
                        if ($addleague == 'true') {
                            $leagueId = sanitize_int($_POST['leagueid']);
                            $sqladdteamleague = "INSERT INTO lms_league_team (lms_league_team_league_id, lms_league_team_team_id) VALUES (:leagueid, :teamid)";
                            $sqladdteamleague = $mypdo->prepare($sqladdteamleague);
                            $sqladdteamleague->execute(array(
                                ':teamid' => $teamid,
                                ':leagueid' => $leagueId
                            ));
                            $leagueadded = $sqladdteamleague->rowCount();
                            if ($leagueadded > 0) {
                                $alerttext .= "Team successfully added to league.";
                            }
                        }
                        if ($upcount > 0 || $leagueadded > 0) {
                            $html .= "<script>
												alert('".$alerttext."');
												window.location.href='team-main.php';
											</script>";
                        } else {
                            $html .= "<script>
										alert('Details not altered.');
										window.location.href='team-main.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
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