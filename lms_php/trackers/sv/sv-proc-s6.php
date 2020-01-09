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
				if ( isset($_POST['id'], $_POST['q42radio'], $_POST['q43radio'], $_POST['q44radio'], $_POST['q45radio'], $_POST['q46radio'], $_POST['q47radio'], $_POST['q48radio'], $_POST['q49radio'], $_POST['q50radio'], $_POST['q51radio'], $_POST['q52radio'], $_POST['q53radio'], $_POST['comments6'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments6'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q42'] = sanitize_paranoid_string($_POST['q42radio']);
					$answerArray['q43'] = sanitize_paranoid_string($_POST['q43radio']);
					$answerArray['q44'] = sanitize_paranoid_string($_POST['q44radio']);
					$answerArray['q45'] = sanitize_paranoid_string($_POST['q45radio']);
					$answerArray['q46'] = sanitize_paranoid_string($_POST['q46radio']);
					$answerArray['q47'] = sanitize_paranoid_string($_POST['q47radio']);
					$answerArray['q48'] = sanitize_paranoid_string($_POST['q48radio']);
					$answerArray['q49'] = sanitize_paranoid_string($_POST['q49radio']);
					$answerArray['q50'] = sanitize_paranoid_string($_POST['q50radio']);
					$answerArray['q51'] = sanitize_paranoid_string($_POST['q51radio']);
					$answerArray['q52'] = sanitize_paranoid_string($_POST['q52radio']);
					$answerArray['q53'] = sanitize_paranoid_string($_POST['q53radio']);
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
						$updateSql = "UPDATE svchks SET na = :na, q42 = :q42, q43 = :q43, q44 = :q44, q45 = :q45, q46 = :q46, q47 = :q47, q48 = :q48, q49 = :q49, q50 = :q50, q51 = :q51, q52 = :q52, q53 = :q53, s6 = :s6, comment6 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q42'=>$answerArray['q42'], ':q43'=>$answerArray['q43'], ':q44'=>$answerArray['q44'], ':q45'=>$answerArray['q45'], ':q46'=>$answerArray['q46'], ':q47'=>$answerArray['q47'], ':q48'=>$answerArray['q48'], ':q49'=>$answerArray['q49'], ':q50'=>$answerArray['q50'], ':q51'=>$answerArray['q51'], ':q52'=>$answerArray['q52'], ':q53'=>$answerArray['q53'], ':s6'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>6, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 7;
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
