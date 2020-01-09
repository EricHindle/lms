<?php
	$myPath='../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start(); 
	if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Admin</title>
			    
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
			                    <h1><strong>Admin</strong></h1>
			                    <br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
			                    	<a href="'.$myPath.'struct/user/user-main.php">
			                    		<h3 class="title" >Users</h3>
			                            <p>User Management</p>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile blue">
			                    	<a href="'.$myPath.'struct/lbo/lbo-main.php">
			                    		<h3 class="title" >LBO</h3>
			                            <p>LBO Management.</p>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
			                    	<a href="'.$myPath.'struct/focus/focus-admin.php">
			                    		<h3 class="title" >Focus</h3>
			                            <p>Focus Management.</p>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile orange">
			                    	<a href="'.$myPath.'struct/data/data-main.php">
			                    		<h3 class="title" >Data</h3>
			                            <p>View/Export Data</p>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile purple">
			                    	<a href="'.$myPath.'struct/report/uload.php">
			                    		<h3 class="title" >Completion report</h3>
			                            <p>Completion report for SV and SVL</p>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile teal">
			                    	<a href="'.$myPath.'struct/questions/question-main.php">
			                    		<h3 class="title" >Questions</h3>
			                            <p>Create and Edit Survey Questions</p>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="'.$myPath.'menus/home.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
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
