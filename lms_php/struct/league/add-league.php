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
            if (isset($_POST['leaguename'], $_POST['leagueabbr'])) {
                $leaguename = sanitize_message_string($_POST['leaguename']);
                $leagueabbr = $_POST['leagueabbr'];
                if ($leaguename && $leagueabbr && strlen($leagueabbr) < 5) {
                    $html = "";
                    $cusql = "SELECT lms_league_id FROM lms_league WHERE lms_league_name = :leaguename LIMIT 1";
                    $cuquery = $mypdo->prepare($cusql);
                    $cuquery->bindParam(':leaguename', $leaguename);
                    $cuquery->execute();
                    $cucount = $cuquery->rowCount();

                    if ($cucount > 0) {
                        $html .= "<script>
										alert('A league with that name already exists.');
										window.location.href='league-main.php';
									</script>";
                    } else {
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $sqladdleague = "INSERT INTO lms_league (lms_league_name, lms_league_abbr, lms_league_supported) VALUES (:leaguename, :leagueabbr, 1)";
                        $stmtaddleague = $mypdo->prepare($sqladdleague);
                        $stmtaddleague->execute(array(
                            ':leaguename' => $leaguename,
                            ':leagueabbr' => $leagueabbr
                        ));
                        $added = $stmtaddleague->rowCount();
                        $html .= "<script>
											alert('" . $added . " leagues added.');
											window.location.href='league-main.php';
										</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='league-main.php';
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