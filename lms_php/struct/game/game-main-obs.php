<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';
sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    $gamesql = "SELECT lms_game_id, lms_game_start_wkno, lms_game_name FROM lms_game WHERE lms_game_manager = :manager and lms_game_status = 1 ORDER BY lms_game_start_wkno, lms_game_name";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(":manager", $_SESSION['user_id'], PDO::PARAM_INT);
    $gamequery->execute();
    $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
    

    $remainingweeks = get_remaining_weeks();
    
    $mygamessql = "SELECT lms_game_id, lms_game_id, lms_game_name FROM v_lms_player_games WHERE lms_player_id = :player and lms_game_player_status = 1 ORDER BY lms_game_name";
    $mygamesquery = $mypdo->prepare($mygamessql);
    $mygamesquery->bindParam(":player", $_SESSION['user_id'], PDO::PARAM_INT);
    $mygamesquery->execute();
    $mygamesfetch = $mygamesquery->fetchAll(PDO::FETCH_ASSOC);
    
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
			                    <div class="tile orange">
			                	<form class="form-horizontal" role="form" name ="editgame" method="post" action="process-leave-game.php">';
    $html .= $key;
    $html .= '					<h3 class="title">Leave a Game</h3>
				                    <div class="form-group" style="margin-left:16px;margin-right:16px">
			                            <select class="form-control" id="gameid" name="gameid">';
    foreach ($mygamesfetch as $myGame) {
        $html .= '<option value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group" style="margin-left:16px;margin-right:16px">
                                        <br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
                                 </form>
			          			</div>
			                </div>
                        </div>
                        <div class="row">
			                <div class="col-sm-8">
			                    <div class="tile green">
			                    	<a href="' . $myPath . 'struct/game/game-manage.php">
			                    		<h3 class="title" >Manage Games</h3>
			                        </a>
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