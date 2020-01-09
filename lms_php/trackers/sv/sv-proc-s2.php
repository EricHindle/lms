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
				if ( isset($_POST['id'], $_POST['q4radio'], $_POST['q5radio'], $_POST['q6radio'], $_POST['q7radio'], $_POST['q8radio'], $_POST['q9radio'], $_POST['q10radio'], $_POST['q11radio'], $_POST['q12radio'], $_POST['q13radio'], $_POST['q14radio'], $_POST['q15radio'], $_POST['comments2'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments2'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q4'] = sanitize_paranoid_string($_POST['q4radio']);
					$answerArray['q5'] = sanitize_paranoid_string($_POST['q5radio']);
					$answerArray['q6'] = sanitize_paranoid_string($_POST['q6radio']);
					$answerArray['q7'] = sanitize_paranoid_string($_POST['q7radio']);
					$answerArray['q8'] = sanitize_paranoid_string($_POST['q8radio']);
					$answerArray['q9'] = sanitize_paranoid_string($_POST['q9radio']);
					$answerArray['q10'] = sanitize_paranoid_string($_POST['q10radio']);
					$answerArray['q11'] = sanitize_paranoid_string($_POST['q11radio']);
					$answerArray['q12'] = sanitize_paranoid_string($_POST['q12radio']);
					$answerArray['q13'] = sanitize_paranoid_string($_POST['q13radio']);
					$answerArray['q14'] = sanitize_paranoid_string($_POST['q14radio']);
					$answerArray['q15'] = sanitize_paranoid_string($_POST['q15radio']);
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
						$updateSql = "UPDATE svchks SET na = :na, q4 = :q4, q5 = :q5, q6 = :q6, q7 = :q7, q8 = :q8, q9 = :q9, q10 = :q10, q11 = :q11, q12 = :q12, q13 = :q13, q14 = :q14, q15 = :q15, s2 = :s2, comment2 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q4'=>$answerArray['q4'], ':q5'=>$answerArray['q5'], ':q6'=>$answerArray['q6'], ':q7'=>$answerArray['q7'], ':q8'=>$answerArray['q8'], ':q9'=>$answerArray['q9'], ':q10'=>$answerArray['q10'], ':q11'=>$answerArray['q11'], ':q12'=>$answerArray['q12'], ':q13'=>$answerArray['q13'], ':q14'=>$answerArray['q14'], ':q15'=>$answerArray['q15'], ':s2'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>2, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 3;
							header('Location: '.$myPath.'trackers/sv/sv-survey.php');
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
