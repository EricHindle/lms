<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/game/game-functions.php';

sec_session_start();
$formKey = new formKey();
$currentPage = 'join';
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gamecode'])) {
                $gamecode = $_POST['gamecode'];
                if ($gamecode) {
                    $html = "";
                    $gamequery = find_game_by_code($gamecode);
                    $gamecount = $gamequery->rowCount();

                    if ($gamecount > 0) {
                        $gamefetch = $gamequery->fetch(PDO::FETCH_ASSOC);
                        $gameplayersql = "SELECT lms_game_id FROM lms_game_player WHERE lms_game_id = :gameid and lms_player_id = :playerid";
                        $gameplayerquery = $mypdo->prepare($gameplayersql);
                        $gameplayerquery->bindParam(":gameid", $gamefetch['lms_game_id'], PDO::PARAM_INT);
                        $gameplayerquery->bindParam(":playerid", $_SESSION['user_id'], PDO::PARAM_INT);
                        $gameplayerquery->execute();
                        $gameplayercount = $gameplayerquery->rowCount();

                        if ($gameplayercount == 0) {
                            $key = $formKey->outputKey();
                            if ($gamefetch["lms_game_status"] == 1) {
                                echo '
								<!doctype html>
								<html>
									<head>
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
			    <link rel="stylesheet" href="' . $myPath . 'css/style.css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />

									    <title>Confirm Join Game</title>
									    

									</head>

									<body>';
                                include $myPath . 'globNAV.php';
                                $html .= '
		      <div class="container" style="min-height:50vh;">
                <div  class="box" style="padding:1em;width:400px;margin:10px;">
									                    <h2>Confirmation</h2>
									                </div>

									     <div class="box" style="padding:1em;width:400px;margin:10px;">';

                                $html .= '	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-confirm-join-game.php">';
                                $html .= $key;
                                $html .= '					<h3 class="text-center">Confirm That You Want To Join This Game</h3>
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


<div style="padding:2em;">
						  <a href="' . $myPath . 'struct/game/game-manage.php" class="btn" style="padding:15px;" role="button">Back</a>
				     </div>


									    	</div>
									</body>
								</html>
									            ';
                            } else {
                                $html .= "<script>
										alert('Game not open to new players. Please check the code and try again.');
										window.location.href='".$myPath."menus/home.php';
									</script>";
                            }
                        } else {
                            $html .= "<script>
										alert('You are already in this game. Please check the code and try again.');
										window.location.href='".$myPath."menus/home.php';
									</script>";
                        }
                    } else {
                        $html .= "<script>
										alert('Game not found. Please check the code and try again.');
										window.location.href='".$myPath."menus/home.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
								alert('Enter a game code and try again.');
								window.location.href='".$myPath."menus/home.php';
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
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>