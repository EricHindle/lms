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
				if ( isset($_POST['id'], $_POST['q33radio'], $_POST['q34radio'], $_POST['q35radio'], $_POST['q36radio'], $_POST['q37radio'], $_POST['q38radio'], $_POST['q39radio'], $_POST['q40radio'], $_POST['q41radio'], $_POST['comments5'])  ) {
					$id = sanitize_int($_POST['id']);
					$comments = filter_var($_POST['comments5'], FILTER_SANITIZE_STRING);
					$answerArray = array();
					$answerArray['q33'] = sanitize_paranoid_string($_POST['q33radio']);
					$answerArray['q34'] = sanitize_paranoid_string($_POST['q34radio']);
					$answerArray['q35'] = sanitize_paranoid_string($_POST['q35radio']);
					$answerArray['q36'] = sanitize_paranoid_string($_POST['q36radio']);
					$answerArray['q37'] = sanitize_paranoid_string($_POST['q37radio']);
					$answerArray['q38'] = sanitize_paranoid_string($_POST['q38radio']);
					$answerArray['q39'] = sanitize_paranoid_string($_POST['q39radio']);
					$answerArray['q40'] = sanitize_paranoid_string($_POST['q40radio']);
					$answerArray['q41'] = sanitize_paranoid_string($_POST['q41radio']);
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
						$updateSql = "UPDATE svchks SET na = :na, q33 = :q33, q34 = :q34, q35 = :q35, q36 = :q36, q37 = :q37, q38 = :q38, q39 = :q39, q40 = :q40, q41 = :q41, s5 = :s5, comment5 = :comment, lastcompsection = :lastSection WHERE id = :id";
						$updateQuery = $mypdo->prepare($updateSql);
						$updateQuery->execute(array(':na'=>$nonapplicable, ':q33'=>$answerArray['q33'], ':q34'=>$answerArray['q34'], ':q35'=>$answerArray['q35'], ':q36'=>$answerArray['q36'], ':q37'=>$answerArray['q37'], ':q38'=>$answerArray['q38'], ':q39'=>$answerArray['q39'], ':q40'=>$answerArray['q40'], ':q41'=>$answerArray['q41'], ':s5'=>$sectionScore, ':comment'=>$comments, ':lastSection'=>5, ':id'=>$id));
						$updateRows = $updateQuery->rowCount();
						if ($updateRows>0) {
							$_SESSION['svid'] = $id;
							$_SESSION['svsec'] = 6;
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
