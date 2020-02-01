<?php
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
            if (isset($_POST['teamname'])) {
                $teamname = sanitize_message_string($_POST['teamname']);
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
                        $sqladdteam = "INSERT INTO lms_team (lms_team_name, lms_team_active) VALUES (:teamname, 1)";
                        $stmtaddteam = $mypdo->prepare($sqladdteam);
                        $stmtaddteam->execute(array(
                            ':teamname' => $teamname
                        ));
                        $added = $stmtaddteam->rowCount();
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