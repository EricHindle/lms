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
	    $html = "";
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
		$html .= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Games</strong></h1>
			                    <br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
		                    		<h3 class="title" >Join a Game</h3>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile blue">
		                    		<h3 class="title" >Create a Game</h3>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
		                    		<h3 class="title" >Manage a Game</h3>
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
							<div class="col-xs-6">
								<a href="'.$myPath.'menus/home.php" class="btn btn-primary btn-lg" role="button">Back</a>
								<br>
							</div>
						</div>
			    	</div>
			    </section>
		</body>
	</html>

		';
echo $html;
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>
