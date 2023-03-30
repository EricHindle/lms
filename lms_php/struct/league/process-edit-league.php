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
            if (isset($_POST['id'], $_POST['leaguename'], $_POST['leagueabbr'], $_POST['cal'])) {
                $leagueid = sanitize_int($_POST['id']);
                $leaguename = $_POST['leaguename'];
                $leagueabbr = $_POST['leagueabbr'];
                $leaguecal = $_POST['cal'];
                $issupported = (isset($_POST['issupported']) ? $_POST['issupported'] : "false");
                $mysupported = ($issupported == "true" ? 1 : 0);

                if ($leagueid && $leaguename && $leagueabbr && strlen($leagueabbr) < 5) {
                    $html = "";

                    $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr, lms_league_supported FROM lms_league WHERE lms_league_id = :id LIMIT 1";
                    $leaguequery = $mypdo->prepare($leaguesql);
                    $leaguequery->execute(array(
                        ':id' => $leagueid
                    ));
                    $leaguecount = $leaguequery->rowCount();
                    if ($leaguecount > 0) {
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $upsql = "UPDATE lms_league SET lms_league_name = :leaguename, lms_league_abbr = :leagueabbr, lms_league_supported = :supported, lms_league_current_calendar = :cal WHERE lms_league_id = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $leagueid, PDO::PARAM_INT);
                        $upquery->bindParam(':leaguename', $leaguename);
                        $upquery->bindParam(':leagueabbr', $leagueabbr);
                        $upquery->bindParam(':cal', $leaguecal, PDO::PARAM_INT);
                        $upquery->bindParam(':supported', $mysupported, PDO::PARAM_INT);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
                            $html .= "<script>
												alert('Details updated successfully.');
												window.location.href='league-main.php';
											</script>";
                        } else {
                            $html .= "<script>
										alert('Details not altered.');
										window.location.href='league-main.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
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