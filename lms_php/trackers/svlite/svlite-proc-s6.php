<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';
	sec_session_start();
	$formKey = new formKey();
	if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    	if(!isset($_POST['form_key']) || !$formKey->validate())
        {
            bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "CSRF failure");
        }
        else
        {
			if(login_check($mypdo) == true) {
				if ( isset($_POST['id'], $_POST['q16radio'], $_POST['q17radio'], $_POST['q18radio'], $_POST['q19radio'], $_POST['q20radio'], $_POST['comments6'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments6'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q16'] = sanitize_paranoid_string($_POST['q16radio']);
					$answerArray['q17'] = sanitize_paranoid_string($_POST['q17radio']);
					$answerArray['q18'] = sanitize_paranoid_string($_POST['q18radio']);
					$answerArray['q19'] = sanitize_paranoid_string($_POST['q19radio']);
					$answerArray['q20'] = sanitize_paranoid_string($_POST['q20radio']);
					$expectedyn = array("Yes","No","NA","0","1","2","3","4","5","Done");
					$answerCheck = true;
					foreach ($answerArray as $key => $value) {
						$answerCheck = in_array($value, $expectedyn, true);
						if ($answerCheck==false) {
							break;
						}
					}
					if ($id && $answerCheck) {
						$questionsAnswered = 0;
						$numscore = 0;
						$nasql = "SELECT na FROM svlitechks WHERE id = :id LIMIT 1";
						$naquery = $mypdo->prepare($nasql);
						$naquery->execute(array(':id'=>$id));
						$nafetch = $naquery->fetch(PDO::FETCH_ASSOC);
						$nonapplicable = $nafetch['na'];
						$sectionScore = 0.0;
						foreach ($answerArray as $key => $value) {
							if ($value=='NA') {
								$nonapplicable++;
							} else {
								$tempnum=0;
								switch ($value) {
									case 'Yes':
										$tempnum=5;
										break;
									case 'No':
										$tempnum=0;
										break;
									case '0':
										$tempnum=0;
										break;
									case '1':
										$tempnum=1;
										break;
									case '2':
										$tempnum=2;
										break;
									case '3':
										$tempnum=3;
										break;
									case '4':
										$tempnum=4;
										break;
									case '5':
										$tempnum=5;
										break;
									case 'Done':
										$tempnum=5;
										break;
								}
								$numscore+=$tempnum;
								$questionsAnswered++;
							}
						}
						if ($numscore>0) {
							$sectionScore = round((($numscore/($questionsAnswered*5)) * 100), 2);
						}
						$updateSql = "UPDATE svlitechks SET na = :na, q16 = :q16, q17 = :q17, q18 = :q18, q19 = :q19, q20 = :q20, s6 = :s6, comment6 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q16'=>$answerArray['q16'], ':q17'=>$answerArray['q17'], ':q18'=>$answerArray['q18'], ':q19'=>$answerArray['q19'], ':q20'=>$answerArray['q20'], ':s6'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>6, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 7;
							header('Location: '.$myPath.'trackers/svlite/svlite-survey.php');
						} else {
							bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not UPDATED in DB");
						}
					} else {
						bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Suspicious data");
					}	
				} else { 
			        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "POST data missing");
				}
			} else { 
			    bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
			}
		}
	} else { 
	    bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Expected POST request");
	}
?>
