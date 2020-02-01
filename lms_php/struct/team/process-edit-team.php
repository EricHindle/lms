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
            if (isset($_POST['id'], $_POST['teamname'])) {
                $gameid = sanitize_int($_POST['id']);
                $teamname = $_POST['teamname'];
                $isactive = $_POST['isactive'];
                $myactive = 0;
                if ($isactive == "true") {
                    $myactive = 1;
                }
                if ($gameid && $teamname) {
                    $html = "";

                    $teamsql = "SELECT lms_team_id, lms_team_name, lms_team_active FROM lms_team WHERE lms_team_id = :id LIMIT 1";
                    $teamquery = $mypdo->prepare($teamsql);
                    $teamquery->execute(array(
                        ':id' => $gameid
                    ));
                    $teamcount = $teamquery->rowCount();
                    if ($teamcount > 0) {
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $upsql = "UPDATE lms_team SET lms_team_name = :teamname, lms_team_active = :active WHERE lms_team_id = :id";
                        $upquery = $mypdo->prepare($upsql);
                        $upquery->bindParam(':id', $gameid, PDO::PARAM_INT);
                        $upquery->bindParam(':teamname', $teamname);
                        $upquery->bindParam(':active', $myactive, PDO::PARAM_INT);
                        $upquery->execute();
                        $upcount = $upquery->rowCount();
                        if ($upcount > 0) {
                            $html .= "<script>
												alert('Details updated successfully.');
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