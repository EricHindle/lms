<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {

    $statussql = "SELECT lms_game_status_id, lms_game_status_text FROM lms_game_status ORDER BY lms_game_status_id ASC";
    $statusquery = $mypdo->prepare($statussql);
    $statusquery->execute();
    $statuslist = $statusquery->fetchAll(PDO::FETCH_ASSOC);

    $html = '';
    echo '
		<!doctype html>
		<html>
			<head>
											<style>
.typeselection {
height: 30px;
width: 100px;
border: none;
border-radius: 2px;
font-size: 16px;
margin-bottom: 4px;
}
.greenbutton {
background-color: #00A600;
}
		</style>    
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
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
			                <div class="col-md-7">
			                    <h1><strong>Admin</strong></h1>
			                    <br>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
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
		                	     <form class="form-horizontal" role="form" name ="gameadmin" method="post" action="' . $myPath . 'struct/game/game-admin.php">';
    $html .= $key;

    $html .= '					     <h3 class="title">Games</h3>
			                         <div class="form-group" style="margin-left:10px;margin-right:10px;margin-bottom:0px">
 
 <button class="typeselection greenbutton" type="submit" name="status" value="0">' . All . '</button></br>';

    foreach ($statuslist as $status) {
        $html .= '<button class="typeselection greenbutton"  type="submit" name="status" value="' . $status['lms_game_status_id'] . '">' . $status['lms_game_status_text']  . '</button></br>';
        
    }
    $html .= '	                    
                                  
			                         </div>
                                  </form>
			                    </div>
			      		    </div>
                        </div>
                        <div class="row">
			                <div class="col-sm-4">
			                    <div class="tile black">
			                    	<a href="' . $myPath . 'struct/week/weekend-admin.php">
			                    		<h3 class="title" >Weekend</h3>
			                            <p>Weekend Processing</p>
			                        </a>	
			          			</div>
			                </div>
			            	<div class="col-sm-4">
			                    <div class="tile teal">
			                    	<a href="' . $myPath . 'struct/week/week-main.php">
			                    		<h3 class="title" >Periods</h3>
			                            <p>Calendar Management</p>
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
			                    <div class="tile blue">
			                    	<a href="' . $myPath . 'struct/league/league-main.php">
			                    		<h3 class="title" >Leagues</h3>
			                            <p>League Management</p>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
                            <div class="col-sm-4">
    		                    <div class="tile purple">
		                	     <form class="form-horizontal" role="form" name ="matchmain" method="post" action="' . $myPath . 'struct/match/match-main.php">';
    $html .= $key;
    $html .= '					     <h3 class="title">Match</h3>
			                         <div class="form-group" style="margin-left:10px;margin-right:10px;margin-bottom:0px">
      			                         <div class="col-sm-8">Manage matches for:</div>
                                         <div class="col-sm-4" style="padding-left: 5px;padding-right: 5px;">
                                              <input type="text" class="form-control" style="height:30px" id="matchperiod" name="matchperiod" maxlength="6" placeholder = "yyyyww" />
                                         </div>
                                         <div class="col-sm-5">
                                             <input id="submit" name="submit" type="submit" value="Select" class="btn btn-primary btn-sm">
                                         </div>
			                         </div>
                                  </form>	
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
    	</html>';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
