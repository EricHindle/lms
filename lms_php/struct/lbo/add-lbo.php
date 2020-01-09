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
	            if (isset($_POST['lbonum'], $_POST['lboname'], $_POST['cluster']))
	            {
	                $lbonum = sanitize_paranoid_string($_POST['lbonum']);
	                $lboname = sanitize_message_string($_POST['lboname']);
	                $cluster = sanitize_int($_POST['cluster']);

	                $lboname=strtoupper(trim($lboname));

	                if($lbonum && $lboname && $cluster)
	                {
	                	$html="";
	                	$cusql = "SELECT id FROM lbos WHERE num = :lbonum LIMIT 1";
						$cuquery = $mypdo->prepare($cusql);
						$cuquery->execute(array(':lbonum' => $lbonum));
						$cucount = $cuquery->rowCount();

						if( $cucount >0){
							$html.= "<script>
										alert('A shop with that number already exists.');
										window.location.href='lbo-main.php';
									</script>";
	                	} else {
		                	$areasql = "SELECT division, region, area FROM lbos WHERE cluster = :cluster LIMIT 1";
							$areaquery = $mypdo->prepare($areasql);
							$areaquery->execute(array(':cluster' => $cluster));
							$areacount = $areaquery->rowCount();

							if ($areacount>0) {
								$areafetch=$areaquery->fetch(PDO::FETCH_ASSOC);

								$name=$_SESSION['username'];
								date_default_timezone_set('Europe/London');
								$phptime = time();
								$mysqltime = date("Y-m-d H:i:s", $phptime);
			                	$sqladduser = "INSERT INTO lbos (num, shop, division, region, area, cluster, created_by, created_on, mod_by, mod_on) VALUES (:num, :shop, :division, :region, :area, :cluster, :created_by, :created_on, :mod_by, :mod_on)";
					            $stmtadduser = $mypdo->prepare($sqladduser);
					            $stmtadduser->execute( array( ':num'=>$lbonum, ':shop'=>$lboname,':division'=>$areafetch['division'], ':region'=>$areafetch['region'], ':area'=>$areafetch['area'], ':cluster'=>$cluster, ':created_by'=>$name, ':created_on'=>$mysqltime, ':mod_by'=>$name, ':mod_on'=>$mysqltime ) );
					            $added = $stmtadduser->rowCount();
								$html.= "<script>
											alert('".$added." shops added.');
											window.location.href='lbo-main.php';
										</script>";

							} else {
								$html.= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='lbo-main.php';
									</script>";
							}
	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='lbo-main.php';
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