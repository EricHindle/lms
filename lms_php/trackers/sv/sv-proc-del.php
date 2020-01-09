<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start();
	
    	
	if(login_check($mypdo) == true) {
		if (isset($_GET['r'])) {
			$survey_id = sanitize_int($_GET['r']);
			if ($survey_id) {
				$check_sql = "SELECT id FROM svchks WHERE id = :id LIMIT 1";
				$check_query = $mypdo->prepare($check_sql);
				$check_query->execute(array(':id'=>$survey_id));
				$check_count = $check_query->rowCount();
				if ($check_count>0) {
					$updateSql = "UPDATE svchks SET fordelete = :fordelete WHERE id = :id";
					$updateQuery = $mypdo->prepare($updateSql);
					$updateQuery->execute(array(':fordelete'=>1, ':id'=>$survey_id));
					$updateRows = $updateQuery->rowCount();
					if ($updateRows>0) {
						echo "
							<script>
								alert('Survey deleted.');
								window.location.href='".$myPath."trackers/sv/sv-incomplete.php';
							</script>
						";
					} else {
						echo "
							<script>
								alert('There was a problem. Please logout and try again.');
								window.location.href='".$myPath."trackers/sv/sv-incomplete.php';
							</script>
						";
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
