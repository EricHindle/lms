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
	            if (isset($_POST['weekyear'],$_POST['weeknumber'],$_POST['weekstart'] ))
	            {
	                $weekyear = $_POST['weekyear'];
	                $weeknumber = $_POST['weeknumber'];
	                $weekstart = $_POST['weekstart'];
	                
	                if($weekyear && $weeknumber)
	                {
	                    $weekid = $weekyear.$weeknumber;
	                	$html="";
	                	$cusql = "SELECT lms_week_no FROM lms_week WHERE lms_week_no = :weekid LIMIT 1";
						$cuquery = $mypdo->prepare($cusql);
						$cuquery->bindParam(':weekid', $weekid);
						$cuquery->execute();
						$cucount = $cuquery->rowCount();

						if( $cucount >0){
							$html.= "<script>
										alert('A week with that number already exists.');
										window.location.href='week-main.php';
									</script>";
	                	} else {
	                	    $dd = date_create($weekstart);
	                	    $ed = date_create($weekstart);
	                	    $sd = date_create($weekstart);
	                	    
                	        date_add($dd,new DateInterval("P2D"));
                	        date_add($ed,new DateInterval("P6D"));
                	        
                	        $deadline = date_format($dd,"Y-m-d");
                	        $enddate = date_format($ed,"Y-m-d");
                	        $startdate = date_format($sd,"Y-m-d");
                	        
							date_default_timezone_set('Europe/London');
							$phptime = time();
							$mysqltime = date("Y-m-d H:i:s", $phptime);
		                	$sqladdweek = "INSERT INTO lms_week (lms_week_no, lms_week, lms_year, lms_week_start, lms_week_end_pick, lms_week_deadline) VALUES (:weekid, :weeknumber, :weekyear, :weekstart, :weekend, :weekdeadline)";
				            $stmtaddweek = $mypdo->prepare($sqladdweek);
				            $stmtaddweek->bindParam(':weekid', $weekid);
				            $stmtaddweek->bindParam(':weeknumber', $weeknumber, PDO::PARAM_INT);
				            $stmtaddweek->bindParam(':weekyear', $weekyear, PDO::PARAM_INT);
				            $stmtaddweek->bindParam(':weekstart',$startdate);
				            $stmtaddweek->bindParam(':weekend', $enddate);
				            $stmtaddweek->bindParam(':weekdeadline', $deadline);
				            
				            $stmtaddweek->execute();
				            $added = $stmtaddweek->rowCount();
							$html.= "<script>
										alert('".$added." values added.');
										window.location.href='week-main.php';
									</script>";

	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='week-main.php';
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