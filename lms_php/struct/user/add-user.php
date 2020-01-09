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
	            if (isset($_POST['username'], $_POST['password'], $_POST['fname'], $_POST['cluster'], $_POST['area'], $_POST['region'], $_POST['division'], $_POST['role']))
	            {
	                $username = sanitize_paranoid_string($_POST['username']);
	                $password = $_POST['password'];
	                $fname = sanitize_message_string($_POST['fname']);
	                $role = sanitize_paranoid_string($_POST['role']);
	                $cluster = sanitize_int($_POST['cluster']);
	                $area = sanitize_int($_POST['area']);
	                $region = sanitize_int($_POST['region']);
	                $division = sanitize_paranoid_string($_POST['division']);
	                $temp = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		            if (!filter_var($temp, FILTER_VALIDATE_EMAIL) === false) {
		            	$allowed = array('williamhill.co.uk', 'williamhill.com');
					    $explodedEmail = explode('@', $temp);
					    $domain = array_pop($explodedEmail);
					    if (in_array($domain, $allowed))
					    {
				        	$valid=TRUE;
				        	$email=$temp;
					    }  
					}
	                if($username && $password && $fname && $role && $cluster && $area && $region && $division && $valid)
	                {
	                	$html="";
	                	$cusql = "SELECT id FROM members WHERE username = :username LIMIT 1";
						$cuquery = $mypdo->prepare($cusql);
						$cuquery->execute(array(':username' => $username));
						$cucount = $cuquery->rowCount();

						if( $cucount >0){
							$html.= "<script>
										alert('Username already in use please pick another username.');
										window.location.href='user-main.php';
									</script>";
	                	} else {
	                		switch ($role) {
	                			case 'BPM':
	                				$workgroup=$role;
	                				$myaccess=$cluster;
	                				$place=$cluster;
	                				$areasql = "SELECT division, region, area, cluster FROM lbos WHERE cluster = :place LIMIT 1";
	                				break;
	                			case 'AM':
	                				$workgroup=$role;
	                				$myaccess= 800 + $area;
	                				$place=$area;
	                				$areasql = "SELECT division, region, area, cluster FROM lbos WHERE area = :place LIMIT 1";
	                				break;
	                			case 'RM':
	                				$workgroup=$role;
	                				$myaccess= 900 + $region;
	                				$place=$region;
	                				$areasql = "SELECT division, region, area, cluster FROM lbos WHERE region = :place LIMIT 1";
	                				break;
	                			case 'DD':
	                				$workgroup=$role;
	                				if ($division=="North") {
	                					$myaccess=951;
	                				}else {
	                					$myaccess=952;
	                				}
	                				$place=$division;
	                				$areasql = "SELECT division, region, area, cluster FROM lbos WHERE division = :place LIMIT 1";
	                				break;
	                			case 'Admin':
	                				$myaccess=999;
	                				$workgroup=$role;
	                				$areasql = "SELECT division, region, area, cluster FROM lbos WHERE division = :place LIMIT 1";
	                				$place="North";
	                				break;
	                		}
		                	
							$areaquery = $mypdo->prepare($areasql);
							$areaquery->execute(array(':place' => $place));
							$areacount = $areaquery->rowCount();

							if ($areacount>0) {
								$areafetch=$areaquery->fetch(PDO::FETCH_ASSOC);
								// $html.= "<script>
								// 		alert('".$areafetch['division']." ".$areafetch['region']." ".$areafetch['area']." ".$cluster." ".$myaccess." ".$email."');
								// 		window.location.href='user-main.php';
								// 	</script>";
								$name=$_SESSION['username'];
								date_default_timezone_set('Europe/London');
								$phptime = time();
								$mysqltime = date("Y-m-d H:i:s", $phptime);
			                	$hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 11]);
			                	$sqladduser = "INSERT INTO members (username, password, fname, email, division, region, area, cluster, role, workgroup , retaccess, created_by, created_on, mod_by, mod_on) VALUES (:username, :password, :fname, :email, :division, :region, :area, :cluster, :role, :workgroup, :retaccess, :created_by, :created_on, :mod_by, :mod_on)";
					            $stmtadduser = $mypdo->prepare($sqladduser);
					            $stmtadduser->execute( array( ':username'=>$username, ':password'=>$hash, ':fname'=>$fname, ':email'=>$email, ':division'=>$areafetch['division'], ':region'=>$areafetch['region'], ':area'=>$areafetch['area'], ':cluster'=>$areafetch['cluster'], ':role'=>$role, ':workgroup'=>$workgroup, ':retaccess'=>$myaccess, ':created_by'=>$name, ':created_on'=>$mysqltime, ':mod_by'=>$name, ':mod_on'=>$mysqltime ) );
					            $added = $stmtadduser->rowCount();
								$html.= "<script>
											alert('".$added." users added.');
											window.location.href='user-main.php';
										</script>";

							} else {
								$html.= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='user-main.php';
									</script>";
							}
	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='user-main.php';
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