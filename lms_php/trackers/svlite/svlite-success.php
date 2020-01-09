<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start(); 
	if (isset($_SESSION['svid'])) {
		unset($_SESSION['svid']);
	}
	if (isset($_SESSION['svsec'])) {
		unset($_SESSION['svsec']);
	}
	if(login_check($mypdo) == true) {
		$messagenum = sanitize_int($_GET['m']);
		$recid=0;
		$recid = sanitize_int($_GET['s']);
		$message="";
		$details = "";
		if($messagenum){
			switch($messagenum){
				case 1:
					$message = "Submitted...";
					if ($recid && $recid!=0) {
						$vesql= "SELECT shop, cem FROM svlitechkscomp WHERE id = :id LIMIT 1";
						$vequery = $mypdo->prepare($vesql);
						$vequery->execute(array(':id'=>$recid));
						$vecount = $vequery->rowCount();
						if($vecount>0){
							$vefetch = $vequery->fetch(PDO::FETCH_ASSOC);
							$details = $vefetch['cem'].' <br> '.$vefetch['shop'];
						}
					
					}
					
					break;
				case 2:
					$message = "Problem. Please log out and try again";
					break;
				default:
					$message = "Problem. Please log out and try again";
			}

		}
		echo '
		<!doctype html>
		<html>
			<head>
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Smart Visit Lite</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
			    <script src="'.$myPath.'js/jquery.js"></script>
			    <script src="'.$myPath.'js/bootstrap.min.js"></script>
			</head>

			<body>';
				include $myPath.'globNAV.php';
		echo '
				<section id="homeSection">
			    <br><br><br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h3 class="addShad">'.$message.'</h3>
			                    <br><br>
			                    <h1 class="text-center">'.$details.'</h1>
			                    <br><br><br>
			                </div>
			            </div>
			            <div class="row">
			                <div class="col-md-12">
			                    <button class="btn btn-primary btn-lg" onclick="location.href=\''.$myPath.'menus/home.php\'">Done</button>
			                </div>
			            </div>
			            <br><br><br><br><br><br><br><br><br><br>
			    	</div>
			    </section>
			</body>
		</html>

		';
	} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
