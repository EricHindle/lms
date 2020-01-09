<?php
	$myPath='../';
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
		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Home</title>
			    
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
			    <br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Welcome ';
			$arr=explode(' ', trim($_SESSION['fname']));
			$firstName=sanitize_paranoid_string($arr[0]);
			if($firstName){echo $firstName;}
								echo '</strong></h1>
			                    <br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
			                    	<a href="'.$myPath.'trackers/sv/sv-main.php">
			                    		<h3 class="title" >Smart Visit</h3>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
			                    	<a href="'.$myPath.'trackers/focus/focus-main.php">
			                    		<h3 class="title" >Focus</h3>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
			';
			echo '
			                <div class="col-sm-4">
			                    <div class="tile orange">
			                    	<a href="'.$myPath.'menus/reports.php">
			                    		<h3 class="title" >Reports</h3>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<br><br><br><br>
		    	</div>
		    </section>
		</body>
	</html>

		';
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}

?>
