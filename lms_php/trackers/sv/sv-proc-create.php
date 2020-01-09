<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "CSRF failure");
    } else {
        if (login_check($mypdo) == true) {
            if (isset($_POST['cem_num'], $_POST['lbo'])) {
                $lboid = sanitize_int($_POST['lbo']);
                $cem_num = sanitize_paranoid_string($_POST['cem_num']);
                if ($lboid && $cem_num) {
                    $vesql = "SELECT id, num, shop, division, region, area, cluster FROM lbos WHERE id = :id LIMIT 1";
                    $vequery = $mypdo->prepare($vesql);
                    $vequery->execute(array(
                        ':id' => $lboid
                    ));
                    $vecount = $vequery->rowCount();
                    if ($vecount > 0) {
                        $vefetch = $vequery->fetch(PDO::FETCH_ASSOC);
                        date_default_timezone_set('Europe/London');
                        $phptime = time();
                        $mysqltime = date("Y-m-d H:i:s", $phptime);
                        $shop = $vefetch['shop'];
                        $division = $vefetch['division'];
                        $region = $vefetch['region'];
                        $area = $vefetch['area'];
                        $cluster = $vefetch['cluster'];
                        $lbonum = $vefetch['num'];
                        $completedBy = $_SESSION['fname'];
							$vesql2= "INSERT INTO svchks (id, num, shop, division, region, area, cluster, datestart, completedby, cem) VALUES (:ID, :num, :shop, :division, :region, :area, :cluster, :datestart, :completedby, :cem)";
                        $vequery2 = $mypdo->prepare($vesql2);
							$vequery2->execute(array(':ID'=>null, ':num'=>$lbonum, ':shop'=>$shop, ':division'=>$division, ':region'=>$region, ':area'=>$area, ':cluster'=>$cluster, ':datestart'=>$mysqltime, ':completedby'=>$completedBy,  ':cem'=>$cem_num));
                        $lastInsert = $mypdo->lastInsertId('id');
                        if ($lastInsert) {
                            $_SESSION['svid'] = $lastInsert;
                            $_SESSION['svsec'] = 1;
                            header('Location: ' . $myPath . 'trackers/sv/sv-survey-new.php');
                        } else {
                            bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not INSERTED in DB");
                        }
                    } else {
                        bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not found in DB");
                    }
                } else {
                    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Suspicious data");
                }
            } else {
                bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "POST data missing");
            }
        } else {
            bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Couldn't verify user");
        }
    }
} else {
    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Expected POST request");
}
?>
