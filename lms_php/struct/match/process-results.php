<?php
	$myPath='../../';

	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';

	sec_session_start();
	$formKey = new formKey();
	$access = sanitize_int($_SESSION['retaccess']);
	if(login_check($mypdo) == true && $access == 999) {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(!isset($_POST['form_key']) || !$formKey->validate()) {
        	            header('Location: '.$myPath.'index.php?error=1');
        	} else {
	            if (isset($_POST['weekid'])){
                    $gameid = $_POST['weekid'];
                    $matchsql = "SELECT lms_match_id, lms_match_date, lms_match_result, lms_team_name FROM v_lms_match WHERE lms_match_weekno = :matchwk";
                    $matchquery = $mypdo->prepare($matchsql);
                    $matchquery->bindParam(':matchwk',$gameid);
                    $matchquery->execute();
                    $matchcount = $matchquery->rowCount();
                	$html="";
    				if ($matchcount>0) {
    				    $matchfetch=$matchquery->fetchAll(PDO::FETCH_ASSOC);
    					date_default_timezone_set('Europe/London');
    					$phptime = time();
    					$mysqltime = date("Y-m-d H:i:s", $phptime);
    					$totalupdates = 0;
    					foreach ($matchfetch as $rs) {
    					    $matchid = $rs['lms_match_id'];
    					    $matchresult =  $_POST["res-".$matchid];
    					    $upsql = "UPDATE lms_match SET lms_match_result = :result WHERE lms_match_id = :id";
    					    $upquery = $mypdo->prepare($upsql);
    					    $upquery->bindParam(':id', $matchid);
    					    $upquery->bindParam(':result', $matchresult);
    					    $upquery->execute();
    					    $upcount = $upquery->rowCount();
    					    $totalupdates += $upcount;
    					}
    					$html.= "<script>
                            	alert('".$totalupdates." matches resulted.');
                            	window.location.href='match-main.php';
                            </script>";
    				} else {
    					$html.= "<script>
    								alert('There was a problem. Please check details and try again.');
    								window.location.href='match-main.php';
    							</script>";
    				}
                } else {
                	$html.= "<script>
								alert('There was a problem with the weekno.');
								window.location.href='match-main.php';
							</script>";
                }
                echo $html;
                }
            } else { 
            header('Location: '.$myPath.'index.php?error=1');
          	}  
        } else { 
                header('Location: '.$myPath.'index.php?error=1');
    }
?>