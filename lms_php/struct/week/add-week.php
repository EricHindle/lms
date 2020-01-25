<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access == 999) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['weekyear'], $_POST['weeknumber'], $_POST['weekstart'], $_POST['weekcount'])) {

                $weekyear = sanitize_int($_POST['weekyear'], 2020);
                $weeknumber = sanitize_int($_POST['weeknumber'], 1);
                $weekstart = sanitize_datetime($_POST['weekstart']);
                $weekcount = sanitize_int($_POST['weekcount'], 1, 52);
                if ($weekyear && $weeknumber && $weekcount && $weekstart) {
                    $totaladded = 0;
                    $lastweek = $weeknumber + $weekcount;
                    $dd = date_create($weekstart);
                    $ed = date_create($weekstart);
                    $sd = date_create($weekstart);
                    for ($wkno = $weeknumber; $wkno < $lastweek; $wkno += 1) {
                        $weekyr = sprintf('%04d', sanitize_int($_POST['weekyear']));
                        $weekno = sprintf('%02d', $wkno);
                        $weekid = $weekyr . $weekno;
                        $html = "";
                        $cusql = "SELECT lms_week_no FROM lms_week WHERE lms_week_no = :weekid LIMIT 1";
                        $cuquery = $mypdo->prepare($cusql);
                        $cuquery->bindParam(':weekid', $weekid);
                        $cuquery->execute();
                        $cucount = $cuquery->rowCount();

                        if ($cucount > 0) {
                            $html .= "<script>
    										alert('A week with that number already exists.');
    										window.location.href='week-main.php';
    									</script>";
                        } else {

                            date_add($dd, new DateInterval("P2D"));
                            date_add($ed, new DateInterval("P6D"));

                            $deadline = date_format($dd, "Y-m-d");
                            $enddate = date_format($ed, "Y-m-d");
                            $startdate = date_format($sd, "Y-m-d");

                            date_default_timezone_set('Europe/London');
                            $phptime = time();
                            $mysqltime = date("Y-m-d H:i:s", $phptime);
                            $sqladdweek = "INSERT INTO lms_week (lms_week_no, lms_week, lms_year, lms_week_start, lms_week_end, lms_week_deadline) VALUES (:weekid, :weeknumber, :weekyear, :weekstart, :weekend, :weekdeadline)";
                            $stmtaddweek = $mypdo->prepare($sqladdweek);
                            $stmtaddweek->bindParam(':weekid', $weekid);
                            $stmtaddweek->bindParam(':weeknumber', $weekno, PDO::PARAM_INT);
                            $stmtaddweek->bindParam(':weekyear', $weekyr, PDO::PARAM_INT);
                            $stmtaddweek->bindParam(':weekstart', $startdate);
                            $stmtaddweek->bindParam(':weekend', $enddate);
                            $stmtaddweek->bindParam(':weekdeadline', $deadline);

                            $stmtaddweek->execute();
                            $added = $stmtaddweek->rowCount();
                            $totaladded += $added;
                            date_add($sd, new DateInterval("P7D"));
                            $dd = clone $sd;
                            $ed = clone $sd;
                        }
                        $html .= "<script>
                                    	alert('" . $totaladded . " values added.');
                                    	window.location.href='week-main.php';
                                    </script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('Missing/invalid values. Please check details and try again.');
										window.location.href='week-main.php';
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