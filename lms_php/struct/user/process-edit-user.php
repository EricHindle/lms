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
	            if (isset($_POST['id'], $_POST['cluster'], $_POST['active'], $_POST['role']))
	            {
	                $id = sanitize_int($_POST['id']);
	                $cluster = sanitize_int($_POST['cluster']);
	                $active = sanitize_int($_POST['active']);
	                $role = sanitize_paranoid_string($_POST['role']);
	                if($id && $cluster)
	                {
	                	$html="";

	                	$areasql = "SELECT division, region, area FROM lbos WHERE cluster = :cluster LIMIT 1";
						$areaquery = $mypdo->prepare($areasql);
						$areaquery->execute(array(':cluster' => $cluster));
						$areacount = $areaquery->rowCount();
						if ($areacount>0) {
								$areafetch=$areaquery->fetch(PDO::FETCH_ASSOC);

								$name=$_SESSION['username'];
								date_default_timezone_set('Europe/London');
								$phptime = time();
								$myaccess = 0;
								switch ($role) {
									case 'BPM':
										$myaccess=$cluster;
										break;
									case 'AM':
										$myaccess= 800 + $areafetch['area'];
										break;
									case 'RM':
										$myaccess= 900 + $areafetch['region'];
										break;
									case 'DD':
										if ($areafetch['division']=='North') {
											$myaccess=951;
										} else {
											$myaccess=952;
										}
										break;
									case 'Admin':
										$myaccess= 999;
										break;
									
									default:
										$myaccess=$areafetch['cluster'];
										break;
								}
								$mysqltime = date("Y-m-d H:i:s", $phptime);
								$upsql = "UPDATE members SET division = :division, region = :region, area = :area,  cluster = :cluster, role = :role, workgroup = :workgroup, retaccess = :access, active = :active, mod_by = :mod_by, mod_on = :mod_on WHERE id = :id";
								$upquery = $mypdo->prepare($upsql);
								$upquery->execute(array(':division'=>$areafetch['division'], ':region'=>$areafetch['region'], ':area'=>$areafetch['area'], ':cluster'=>$cluster, ':role'=>$role, ':workgroup'=>$role, ':access'=>$myaccess, ':active'=>$active, ':mod_by'=>$name, ':mod_on'=>$mysqltime, ':id'=>$id));
								$upcount = $upquery->rowCount();
								if( $upcount >0){
									$html.= "<script>
												alert('Details updated successfully.');
												window.location.href='user-main.php';
											</script>";
			                	} else {
			                		$html.= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='user-main.php';
									</script>";
								}
								

						}else{
							$html.= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='user-main.php';
									</script>";
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