<?php
	$myPath='../../';

	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';

	sec_session_start();
	$formKey = new formKey();
	$access = sanitize_int($_SESSION['retaccess']);
	if(login_check($mypdo) == true && $access == 999) {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
	        if(!isset($_POST['form_key']) || !$formKey->validate())
	        {
	            header('Location: '.$myPath.'index.php?error=1');
	        }
	        else
	        {
	            if (isset($_POST['id'], $_POST['email'], $_POST['fname'], $_POST['sname'], $_POST['screenname']))
	            {
	                $id = sanitize_int($_POST['id']);
	                $fname = sanitize_paranoid_string($_POST['fname']);
	                $sname = sanitize_paranoid_string($_POST['sname']);
	                $screenname = sanitize_paranoid_string($_POST['screenname']);
	                $email = sanitize_email_string($_POST['email']);
	                $isadmin = $_POST['isadmin'];
	                if($id)
	                {
	                	$html="";
                        $myaccess = 0;
                        if($isadmin == "true"){
                            $myaccess = 999;
                        }
								$mysqltime = date("Y-m-d H:i:s", $phptime);
								$upsql = "UPDATE lms_player SET lms_player_email = :email, lms_player_login = :username, lms_player_forename = :forename,  lms_player_surname = :surname, lms_player_screen_name = :screenname, lms_access = :access WHERE lms_player_id = :id";
								$upduser = $mypdo->prepare($upsql);
								$upduser->bindParam(':username', $email);
								$upduser->bindParam(':forename', $fname);
								$upduser->bindParam(':surname', $sname);
								$upduser->bindParam(':screenname', $screenname);
								$upduser->bindParam(':email', $email);
								$upduser->bindParam(':access', $myaccess, PDO::PARAM_INT);
								$upduser->bindParam(':id', $id, PDO::PARAM_INT);
								$upduser->execute();
								
								$upcount = $upduser->rowCount();
								if( $upcount >0){
									$html.= "<script>
												alert('Details updated successfully.');
												window.location.href='player-main.php';
											</script>";
			                	} else {
			                		$html.= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='player-main.php';
									</script>";
								}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='player-main.php';
									</script>";
	            	}
	            } else {
	                header('Location: '.$myPath.'index.php?error=1');
	            }
	        }
	    } else { 
	        header('Location: '.$myPath.'index.php?error=1');
		}


	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>