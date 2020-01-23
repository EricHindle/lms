<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gameid'])) {
                $gameid = $_POST['gameid'];
                if ($gameid) {

                    $html = "";
                    $gamesql = "SELECT lms_game_id, lms_game_name, lms_game_manager, lms_game_status, lms_player_screen_name, lms_game_start_wkno FROM v_lms_game WHERE lms_game_id = :id";
                    $gamequery = $mypdo->prepare($gamesql);
                    $gamequery->execute(array(
                        ':id' => $gameid
                    ));
                    $gamecount = $gamequery->rowCount();

                    if ($gamecount > 0) {
                        $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
                        $gameplayersql = "SELECT lms_game_id FROM lms_game_player WHERE lms_game_id = :gameid and lms_player_id = :playerid";
                        $gameplayerquery = $mypdo->prepare($gameplayersql);
                        $gameplayerquery->bindParam(":gameid", $gamefetch['lms_game_id'], PDO::PARAM_INT);
                        $gameplayerquery->bindParam(":playerid", $_SESSION['user_id'], PDO::PARAM_INT);
                        $gameplayerquery->execute();
                        $gameplayercount = $gameplayerquery->rowCount();

                        if ($gameplayercount == 1) {

                            $key = $formKey->outputKey();

                            $isactive = "";
                            if ($gamefetch["lms_game_status"] == 'starting') {
                                echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Confirm Leave Game</title>
									    
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
									                <div class="col-md-8">
									                    <h1><strong>Confirmation</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

                                $html .= '			<div class="well col-md-8 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-confirm-leave-game.php">';
                                $html .= $key;
                                $html .= '					<h3 class="text-center">Confirm That You Want To Leave This Game</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="control-label col-sm-2" for="name" id="name">Name:</label>
																<div class="col-sm-3">
																 	<p class="form-control-static" name="name">' . $gamefetch['lms_game_name'] . '</p>
																</div>

																<label class="control-label col-sm-3" for="stwk" id="stwk">Starting Week:</label>
																<div class="col-sm-3">
																 	<p class="form-control-static" name="stwk">' . $gamefetch['lms_game_start_wkno'] . '</p>
																</div>
															</div>

										                    <div class="form-group">
																<label class="control-label col-sm-2" for="manager">Manager:</label>
																<div class="col-sm-4">
																 	<p class="form-control-static" name="manager" id="manager">' . $gamefetch['lms_player_screen_name'] . '</p>
																</div>
															   <input type= "hidden" name= "gameid" value="' . $gamefetch['lms_game_id'] . '" />
										                    </div>
<div class="text-center">
										                    	<br>
										                        <input id="submit" name="submit" type="submit" value="Confirm" class="btn btn-primary">
</div>
										                </form>
										            </div>
										        </div>
										        <div class="row">
													<br>
													<div class="col-xs-6">
														<a href="' . $myPath . 'struct/game/game-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
                                $html .= "<script>
										alert('Game not open to new players. Please check the code and try again.');
										window.location.href='game-main.php';
									</script>";
                            }
                        
                    } else {
                        $html .= "<script>
										alert('You are not active in this game. Please check the game and try again.');
										window.location.href='game-main.php';
									</script>";
                    }
                } else {
                    $html .= "<script>
										alert('Game not found. Please check the code and try again.');
										window.location.href='game-main.php';
									</script>";
                }

                echo $html;
            } else {
                echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='join-game.php';
									</script>";
            }
        } else {
            header('Location: ' . $myPath . 'index.php?error=1');
        }
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}


	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>