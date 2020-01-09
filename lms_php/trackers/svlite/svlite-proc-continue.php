<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start();	
	if(login_check($mypdo) == true) {
		if (isset($_SESSION['svid'])) {
			unset($_SESSION['svid']);
		}
		if (isset($_SESSION['svsec'])) {
			unset($_SESSION['svsec']);
		}
		if (isset($_GET['r'])) {
			$survey_id = sanitize_int($_GET['r']);
			if ($survey_id) {
				$check_sql = "SELECT lastcompsection FROM svlitechks WHERE id = :id LIMIT 1";
				$check_query = $mypdo->prepare($check_sql);
				$check_query->execute(array(':id'=>$survey_id));
				$check_count = $check_query->rowCount();
				if ($check_count>0) {
					$section = 0;
					$check_fetch = $check_query->fetch(PDO::FETCH_ASSOC);
					$_SESSION['svid'] = $survey_id;
					$section = $check_fetch['lastcompsection'] + 1;
					$_SESSION['svsec'] = $section;
					if ($section==8) {
						header('Location: '.$myPath.'trackers/svlite/svlite-proc-s8.php');
					} else {
						header('Location: '.$myPath.'trackers/svlite/svlite-survey.php');
					}		
				} else {
					bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not found in DB");
				}
			} else {
				bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Suspicious data");
			}
		} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "GET data missing");
		}
	} else { 
	    bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
