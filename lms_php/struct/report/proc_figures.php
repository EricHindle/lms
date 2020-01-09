<?php
	$myPath = '';
	require $myPath . 'includes/db_connect.php';
	require $myPath . 'includes/functions.php';

	$updateSql = "UPDATE bpm_comp_rates SET total = :total WHERE cluster = :cluster";
	$updateQuery = $mypdo->prepare($updateSql);
	
	
	$int_check = TRUE;
	$failvalue ="";
	foreach ($_POST['cluster'] as $key => $value) {
		$int_check = is_numeric($value);
		if ($int_check==false) {
			$failvalue = 'K:'.$key.'V:'.$value;
			break;
		}
	}
	
	if ($int_check) {
		foreach ($_POST['cluster'] as $key => $value) {
			//echo '<br>Cluster '.$key.': '.$value;
			$updateQuery->execute(array(':cluster'=>$key, ':total'=>$value));
			$updateRows = $updateQuery->rowCount();
			if ($updateRows>0) {
				echo '<br>Cluster '.$key.': '.$value;
			} else {
				echo '<br>FAIL Cluster '.$key.': '.$value;
			}
			
		}
	} else {
		echo 'int_check failed <br>'.$failvalue;
	}
	