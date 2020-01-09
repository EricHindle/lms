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
				if ( isset($_POST['id'], $_POST['q54radio'], $_POST['q55radio'], $_POST['q56radio'], $_POST['q57radio'], $_POST['q58radio'], $_POST['q59radio'], $_POST['q60radio'], $_POST['q61radio'], $_POST['q62radio'], $_POST['q63radio'], $_POST['comments7'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments7'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q54'] = sanitize_paranoid_string($_POST['q54radio']);
					$answerArray['q55'] = sanitize_paranoid_string($_POST['q55radio']);
					$answerArray['q56'] = sanitize_paranoid_string($_POST['q56radio']);
					$answerArray['q57'] = sanitize_paranoid_string($_POST['q57radio']);
					$answerArray['q58'] = sanitize_paranoid_string($_POST['q58radio']);
					$answerArray['q59'] = sanitize_paranoid_string($_POST['q59radio']);
					$answerArray['q60'] = sanitize_paranoid_string($_POST['q60radio']);
					$answerArray['q61'] = sanitize_paranoid_string($_POST['q61radio']);
					$answerArray['q62'] = sanitize_paranoid_string($_POST['q62radio']);
					$answerArray['q63'] = sanitize_paranoid_string($_POST['q63radio']);
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
						$updateSql = "UPDATE svchks SET na = :na, q54 = :q54, q55 = :q55, q56 = :q56, q57 = :q57, q58 = :q58, q59 = :q59, q60 = :q60, q61 = :q61, q62 = :q62, q63 = :q63, s7 = :s7, comment7 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q54'=>$answerArray['q54'], ':q55'=>$answerArray['q55'], ':q56'=>$answerArray['q56'], ':q57'=>$answerArray['q57'], ':q58'=>$answerArray['q58'], ':q59'=>$answerArray['q59'], ':q60'=>$answerArray['q60'], ':q61'=>$answerArray['q61'], ':q62'=>$answerArray['q62'], ':q63'=>$answerArray['q63'], ':s7'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>7, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 8;
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
