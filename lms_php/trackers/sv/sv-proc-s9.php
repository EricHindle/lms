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
				if ( isset($_POST['id'], $_POST['q70radio'], $_POST['q71radio'], $_POST['q72radio'], $_POST['q73radio'], $_POST['q74radio'], $_POST['q75radio'], $_POST['q76radio'], $_POST['q77radio'], $_POST['q78radio'], $_POST['q79radio'], $_POST['q80radio'], $_POST['comments9'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments9'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q70'] = sanitize_paranoid_string($_POST['q70radio']);
					$answerArray['q71'] = sanitize_paranoid_string($_POST['q71radio']);
					$answerArray['q72'] = sanitize_paranoid_string($_POST['q72radio']);
					$answerArray['q73'] = sanitize_paranoid_string($_POST['q73radio']);
					$answerArray['q74'] = sanitize_paranoid_string($_POST['q74radio']);
					$answerArray['q75'] = sanitize_paranoid_string($_POST['q75radio']);
					$answerArray['q76'] = sanitize_paranoid_string($_POST['q76radio']);
					$answerArray['q77'] = sanitize_paranoid_string($_POST['q77radio']);
					$answerArray['q78'] = sanitize_paranoid_string($_POST['q78radio']);
					$answerArray['q79'] = sanitize_paranoid_string($_POST['q79radio']);
					$answerArray['q80'] = sanitize_paranoid_string($_POST['q80radio']);
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
						$nasql = "SELECT na FROM svchks WHERE id = :id LIMIT 1";
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
						$updateSql = "UPDATE svchks SET na = :na, q70 = :q70, q71 = :q71, q72 = :q72, q73 = :q73, q74 = :q74, q75 = :q75, q76 = :q76, q77 = :q77, q78 = :q78, q79 = :q79, q80 = :q80, s9 = :s9, comment9 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q70'=>$answerArray['q70'], ':q71'=>$answerArray['q71'], ':q72'=>$answerArray['q72'], ':q73'=>$answerArray['q73'], ':q74'=>$answerArray['q74'], ':q75'=>$answerArray['q75'], ':q76'=>$answerArray['q76'], ':q77'=>$answerArray['q77'], ':q78'=>$answerArray['q78'], ':q79'=>$answerArray['q79'], ':q80'=>$answerArray['q80'], ':s9'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>9, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 10;
							header('Location: '.$myPath.'trackers/sv/sv-proc-s10.php');
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
