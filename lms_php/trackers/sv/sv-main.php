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
		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Smart Visit</title>
			    
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
			                    <h3 class="addShad"><strong>Picks</strong></h3>
			                    <br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
			                    	<a href="'.$myPath.'trackers/sv/sv-start.php">
			                    		<h3 class="title" >Make a pick</h3>
			                        </a>	
			          			</div>
			                </div>

			                <div class="col-sm-4">
			                    <div class="tile blue">
			                    	<a href="'.$myPath.'trackers/sv/sv-incomplete.php">
			                    		<h3 class="title" >See my picks</h3>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
							<div class="col-xs-6">
								<a href="'.$myPath.'menus/home.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
							</div>
						</div>
						<br><br><br>
			    	</div>
			    </section>
		    ';
	echo '
		</body>
	</html>

		';
	} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
