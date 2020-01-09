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
				if ( isset($_POST['id'], $_POST['q21radio'], $_POST['q22radio'], $_POST['q23radio'], $_POST['q24radio'], $_POST['q25radio'], $_POST['q26radio'], $_POST['q27radio'], $_POST['q28radio'], $_POST['q29radio'], $_POST['q30radio'], $_POST['q31radio'], $_POST['q32radio'], $_POST['comments4'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments4'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q21'] = sanitize_paranoid_string($_POST['q21radio']);
					$answerArray['q22'] = sanitize_paranoid_string($_POST['q22radio']);
					$answerArray['q23'] = sanitize_paranoid_string($_POST['q23radio']);
					$answerArray['q24'] = sanitize_paranoid_string($_POST['q24radio']);
					$answerArray['q25'] = sanitize_paranoid_string($_POST['q25radio']);
					$answerArray['q26'] = sanitize_paranoid_string($_POST['q26radio']);
					$answerArray['q27'] = sanitize_paranoid_string($_POST['q27radio']);
					$answerArray['q28'] = sanitize_paranoid_string($_POST['q28radio']);
					$answerArray['q29'] = sanitize_paranoid_string($_POST['q29radio']);
					$answerArray['q30'] = sanitize_paranoid_string($_POST['q30radio']);
					$answerArray['q31'] = sanitize_paranoid_string($_POST['q31radio']);
					$answerArray['q32'] = sanitize_paranoid_string($_POST['q32radio']);
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
						$updateSql = "UPDATE svchks SET na = :na, q21 = :q21, q22 = :q22, q23 = :q23, q24 = :q24, q25 = :q25, q26 = :q26, q27 = :q27, q28 = :q28, q29 = :q29, q30 = :q30, q31 = :q31, q32 = :q32, s4 = :s4, comment4 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q21'=>$answerArray['q21'], ':q22'=>$answerArray['q22'], ':q23'=>$answerArray['q23'], ':q24'=>$answerArray['q24'], ':q25'=>$answerArray['q25'], ':q26'=>$answerArray['q26'], ':q27'=>$answerArray['q27'], ':q28'=>$answerArray['q28'], ':q29'=>$answerArray['q29'], ':q30'=>$answerArray['q30'], ':q31'=>$answerArray['q31'], ':q32'=>$answerArray['q32'], ':s4'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>4, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 5;
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
