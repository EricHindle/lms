<?php
$myPath = '../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
sec_session_start();
if (login_check($mypdo) == true && $_SESSION['retaccess'] == 999) {
    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			</head>

			<body>';
    include $myPath . 'globNAV.php';
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
			                    	<a href="' . $myPath . 'struct/player/player-main.php">
			                    		<h3 class="title" >Players</h3>
			                            <p>Player Management</p>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
			                    	<a href="' . $myPath . 'struct/game/game-admin.php">
			                    		<h3 class="title" >Games</h3>
			                            <p>Game Management.</p>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile orange">
			                    	<a href="' . $myPath . 'struct/team/team-main.php">
			                    		<h3 class="title" >Teams</h3>
			                            <p>Team Management</p>
			                        </a>	
			          			</div>
			                </div>
			            	<div class="col-sm-4">
			                    <div class="tile purple">
			                    	<a href="' . $myPath . 'struct/match/match-main.php">
			                    		<h3 class="title" >Match</h3>
			                            <p>Match Management</p>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile teal">
			                    	<a href="' . $myPath . 'struct/week/week-main.php">
			                    		<h3 class="title" >Periods</h3>
			                            <p>Calendar Management</p>
			                        </a>	
			          			</div>
			                </div>
			            	<div class="col-sm-4">
			                    <div class="tile grey">
			                    	<a href="' . $myPath . 'struct/info/info-main.php">
			                    		<h3 class="title" >Info</h3>
			                            <p>Configuration Management</p>
			                        </a>	
			          			</div>
			                </div>

			      		</div>



			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
