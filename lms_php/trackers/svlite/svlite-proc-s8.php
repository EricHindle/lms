<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start();
	
    	
	if(login_check($mypdo) == true) {
		if (isset($_SESSION['svid'], $_SESSION['svsec'])) {
			$survey_id = sanitize_int($_SESSION['svid']);
			$current_section = sanitize_int($_SESSION['svsec']);
			unset($_SESSION['svid']);
			unset($_SESSION['svsec']);
			if ($survey_id && $current_section==8) {
				$tally_sql = "SELECT q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17, q18, q19, q20, q21, q22, q23, q24, q25, q26, q27, q28, q29, q30 FROM svlitechks WHERE id = :id LIMIT 1";
				$tally_query = $mypdo->prepare($tally_sql);
				$tally_query->execute(array(':id'=>$survey_id));
				$tally_count = $tally_query->rowCount();
				if ($tally_count>0) {
					$tally_fetch = $tally_query->fetch(PDO::FETCH_ASSOC);
					$expectedyn = array("Yes","No","NA","0","1","2","3","4","5","Done");
					$answerCheck = true;
					foreach ($tally_fetch as $key => $value) {
						$answerCheck = in_array($value, $expectedyn, true);
						if ($answerCheck==false) {
							break;
						}
					}
					if ($answerCheck) {
						date_default_timezone_set('Europe/London');
						$phptime = time();
						$mysqltime = date ("Y-m-d H:i:s", $phptime);
						$currentperiod = get_current_period($phptime, $mypdo);
						$numscore = 0;
						$percscore = 0.0;
						$questionsAnswered = 0;
						foreach ($tally_fetch as $key => $value) {
							if ($value!='NA') {

								$questionsAnswered++;
								switch ($value) {
									case 'Yes':
										$numscore+=5;
										break;
									case 'No':
										$numscore+=0;
										break;
									case '0':
										$numscore+=0;
										break;
									case '1':
										$numscore+=1;
										break;
									case '2':
										$numscore+=2;
										break;
									case '3':
										$numscore+=3;
										break;
									case '4':
										$numscore+=4;
										break;
									case '5':
										$numscore+=5;
										break;
									case 'Done':
										$numscore+=5;
										break;
								}
							}
						}
						if ($numscore>0) {
							$percscore = round((($numscore/($questionsAnswered*5)) * 100), 2);
						}
						$updateSql = "UPDATE svlitechks SET datecomplete = :datecomplete, score = :score, veperiod = :veperiod, lastcompsection = :lastcompsection  WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':datecomplete'=>$mysqltime, ':score'=>$percscore, ':veperiod'=>$currentperiod, ':lastcompsection'=>8, ':id'=>$survey_id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$pull_sql = "SELECT * FROM svlitechks WHERE id = :id LIMIT 1";
							$pull_query = $mypdo->prepare($pull_sql);
							$pull_query->execute(array(':id'=>$survey_id));
							$pull_rows = $pull_query->rowCount();
							if ($pull_rows>0) {
								$pull_fetch = $pull_query->fetch(PDO::FETCH_ASSOC);

								$push_sql= "INSERT INTO svlitechkscomp (id, num, shop, division, region, area, cluster, datestart, datecomplete, completedby, cem, score, na, lat, lon, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17, q18, q19, q20, q21, q22, q23, q24, q25, q26, q27, q28, q29, q30, s1, s2, s3, s4, s5, s6, s7, comment1, comment2, comment3, comment4, comment5, comment6, comment7, veperiod) VALUES (:ID, :num, :shop, :division, :region, :area, :cluster, :datestart, :datecomplete, :completedby, :cem, :score, :na, :lat, :lon, :q1, :q2, :q3, :q4, :q5, :q6, :q7, :q8, :q9, :q10, :q11, :q12, :q13, :q14, :q15, :q16, :q17, :q18, :q19, :q20, :q21, :q22, :q23, :q24, :q25, :q26, :q27, :q28, :q29, :q30, :s1, :s2, :s3, :s4, :s5, :s6, :s7, :comment1, :comment2, :comment3, :comment4, :comment5, :comment6, :comment7, :veperiod)";
								$push_query = $mypdo->prepare($push_sql);
								$push_query->execute(array(':ID'=>null, ':num'=>$pull_fetch['num'], ':shop'=>$pull_fetch['shop'], ':division'=>$pull_fetch['division'], ':region'=>$pull_fetch['region'], ':area'=>$pull_fetch['area'], ':cluster'=>$pull_fetch['cluster'], ':datestart'=>$pull_fetch['datestart'], ':datecomplete'=>$pull_fetch['datecomplete'], ':completedby'=>$pull_fetch['completedby'],  ':cem'=>$pull_fetch['cem'], ':score'=>$pull_fetch['score'], ':na'=>$pull_fetch['na'], ':lat'=>$pull_fetch['lat'], ':lon'=>$pull_fetch['lon'], ':q1'=>$pull_fetch['q1'] , ':q2'=>$pull_fetch['q2'] , ':q3'=>$pull_fetch['q3'] , ':q4'=>$pull_fetch['q4'] , ':q5'=>$pull_fetch['q5'] , ':q6'=>$pull_fetch['q6'] , ':q7'=>$pull_fetch['q7'] , ':q8'=>$pull_fetch['q8'] , ':q9'=>$pull_fetch['q9'] , ':q10'=>$pull_fetch['q10'] , ':q11'=>$pull_fetch['q11'] , ':q12'=>$pull_fetch['q12'] , ':q13'=>$pull_fetch['q13'] , ':q14'=>$pull_fetch['q14'] , ':q15'=>$pull_fetch['q15'] , ':q16'=>$pull_fetch['q16'] , ':q17'=>$pull_fetch['q17'] , ':q18'=>$pull_fetch['q18'] , ':q19'=>$pull_fetch['q19'] , ':q20'=>$pull_fetch['q20'] , ':q21'=>$pull_fetch['q21'] , ':q22'=>$pull_fetch['q22'] , ':q23'=>$pull_fetch['q23'] , ':q24'=>$pull_fetch['q24'] , ':q25'=>$pull_fetch['q25'] , ':q26'=>$pull_fetch['q26'] , ':q27'=>$pull_fetch['q27'] , ':q28'=>$pull_fetch['q28'] , ':q29'=>$pull_fetch['q29'] , ':q30'=>$pull_fetch['q30'] , ':s1'=>$pull_fetch['s1'], ':s2'=>$pull_fetch['s2'], ':s3'=>$pull_fetch['s3'], ':s4'=>$pull_fetch['s4'], ':s5'=>$pull_fetch['s5'], ':s6'=>$pull_fetch['s6'], ':s7'=>$pull_fetch['s7'], ':comment1'=>$pull_fetch['comment1'],  ':comment2'=>$pull_fetch['comment2'],  ':comment3'=>$pull_fetch['comment3'],  ':comment4'=>$pull_fetch['comment4'],  ':comment5'=>$pull_fetch['comment5'],  ':comment6'=>$pull_fetch['comment6'],  ':comment7'=>$pull_fetch['comment7'], ':veperiod'=>$pull_fetch['veperiod']));
								$lastInsert = $mypdo->lastInsertId('id');
								if($lastInsert)
								{
									$lbos_sql= "UPDATE lbos SET lastsvlitecomplete = :datecomplete, lastsvlitecheckid = :lastinsert, svliteperiod = :veperiod WHERE num = :num";
									$lbos_query = $mypdo->prepare($lbos_sql);
									$lbos_query->execute(array(':datecomplete'=>$pull_fetch['datecomplete'], ':lastinsert'=>$lastInsert, ':veperiod'=>$currentperiod, ':num'=>$pull_fetch['num']));
									$lbos_count = $lbos_query->rowCount();
									if ($lbos_count>0) {
										$tidy_sql = "UPDATE svlitechks SET iscomplete = :iscomplete, fordelete = :fordelete WHERE id = :id";
										$tidy_query = $mypdo->prepare($tidy_sql);
										$tidy_query->execute(array(':iscomplete'=>1, ':fordelete'=>1, ':id'=>$survey_id));
										$tidy_rows = $tidy_query->rowCount();
										if ($tidy_rows>0) {
											header('Location: svlite-success.php?m=1&s='.$lastInsert);
										} else {
											header('Location: svlite-success.php?m=2&s=0');
										}
									} else {
										header('Location: svlite-success.php?m=2&s=0');
									}
								} else {
									header('Location: svlite-success.php?m=2&s=0');
								}
							} else {
								bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not found in DB 2");
							}
						} else {
							bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not UPDATED in DB");
						}
					} else {
						bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Unexpected data");
					}
				} else {
					bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not found in DB");
				}
			} else {
				bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Suspicious data");
			}
		} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Session data not set");
		}
	} else { 
	    bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
