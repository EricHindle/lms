<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start();
    $access = sanitize_int($_SESSION['retaccess']);
	if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Data Admin</title>
			    
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
			                    <h1><strong>Data Admin</strong></h1>
			                    <br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
			                    	<a href="'.$myPath.'struct/player/player-main.php">
			                    		<h3 class="title" >Players</h3>
			                            <p>Player Management</p>
			                        </a>	
			          			</div>
			                </div>
			            	<div class="col-sm-4">
			                    <div class="tile orange">
			                    	<a href="'.$myPath.'struct/data/data-main.php">
			                    		<h3 class="title" >Data</h3>
			                            <p>View/Data Management</p>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
							<br>
							<div class="col-sm-12">
								<a href="'.$myPath.'struct/main.php" class="btn btn-primary btn-lg" role="button">Back</a>
								<br>
							</div>
						</div>
						<br><br>
		    	</div>
		    </section>
		</body>
	</html>

		';
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>
