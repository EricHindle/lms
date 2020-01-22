<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
if (isset($_SESSION['svid'])) {
    unset($_SESSION['svid']);
}
if (isset($_SESSION['svsec'])) {
    unset($_SESSION['svsec']);
}
if (login_check($mypdo) == true) {
    $gamesql = "SELECT lms_game_id, lms_game_name FROM lms_game WHERE lms_game_manager = :manager and lms_game_status = 'starting' ORDER BY lms_game_start_wkno, lms_game_name";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(":manager", $_SESSION['user_id'], PDO::PARAM_INT);
    $gamequery->execute();
    $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
    $weeksql = "SELECT lms_week_no, lms_week, lms_week, lms_week_start FROM lms_week WHERE lms_week > :week and lms_year = :season";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $_SESSION['currentweek'], PDO::PARAM_INT);
    $weekquery->bindParam(":season", $_SESSION['currentseason'], PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
    $html = "";
    $key = $formKey->outputKey();
    echo '
      <!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Home</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			</head>

			<body>';
    include $myPath . 'globNAV.php';
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
					                	<form role="form" name ="edit" method="post" action="process-join-game.php">';
    $html .= $key;
    $html .= '                  <div class="form-group " style="margin-left:16px;margin-right:16px">
    					                        <input type="text" class="form-control" id="gamecode" name="gamecode" placeholder="game code">
						                    </div>
						                    <div class="form-group" style="margin-left:16px;margin-right:16px">
    					                    	<br>
    					                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
						                    </div>
						                </form>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile blue">
                                    <h3 class="title" >Create a Game</h3>
                                    <form class="form-horizontal" role="form" name ="addteam" method="post" action="add-game.php">';
    $html .= $key;
    $html .= '					         <div class="form-group" style="margin-left:16px;margin-right:16px">
				                    	       <label for="gamename">Game Name:</label>
					                           <input type="text" class="form-control" id="gamename" name="gamename" placeholder="Game name" />
				                         </div>
                                         <div class="form-group" style="margin-left:16px;margin-right:16px">
                                               <label for="gamestartweek">Start week:</label>
                                               <select class="form-control" id="gamestartweek" name="gamestartweek">';
    foreach ($weekfetch as $wk) {
        $html .= '<option value="' . $wk['lms_week_no'] . '">' . $wk['lms_week'] . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
    }
    $html .= '	                               </select>
										 </div>
        				                 <div class="form-group" style="margin-left:16px;margin-right:16px">
                                                <br>
				                                <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                         </div>
                                   </form>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
			                	<form class="form-horizontal" role="form" name ="editgame" method="post" action="edit-game.php">';
    $html .= $key;
    $html .= '					<h3 class="title">Manage Game</h3>
				                    <div class="form-group" style="margin-left:16px;margin-right:16px">
			                            <select class="form-control" id="gameid" name="gameid">';
    foreach ($gamefetch as $myGame) {
        $html .= '<option value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group" style="margin-left:16px;margin-right:16px">
                                        <br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
							<div class="col-xs-6">
								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-lg" role="button">Back</a>
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
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
