<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $status = 0;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['status'])) {

                $status = $_POST['status'];
            }
        }
    }

                $gamesql = "SELECT lms_game_id, lms_game_start_wkno, lms_game_name, lms_game_code, lms_game_status, lms_game_status_text, lms_game_total_players, lms_game_still_active, lms_week, lms_year, lms_player_screen_name FROM v_lms_game ORDER BY lms_game_start_wkno, lms_game_name";
                $gamequery = $mypdo->prepare($gamesql);
                $gamequery->bindParam(":manager", $_SESSION['user_id'], PDO::PARAM_INT);
                $gamequery->execute();
                $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);

                $html = "";
                $key = $formKey->outputKey();
                echo '
      <!doctype html>
		<html>
			<head>
											<style>
.gameselection {
height: 25px;
width: 180px;
border: none;
border-radius: 2px;
font-size: 16px;
}
.greenbutton {
background-color: #00A600;
}

		</style>    

			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>Managed Games</title>
			    
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
			                <div class="col-md-9">
			                    <h1><strong>Managed Games</strong></h1>
			                    <br>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			            </div>
                        <div class="row">


			            	<div class="well col-md-10  textDark">
				        		<h3>All Games</h3>
			                	<form class="form-horizontal" role="form" name ="showgame" method="post" action="show-game-admin.php">
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="game">
										<th>Name</th>
										<th>Start Wk</th>
                                        <th>Game Status</th>
                                        <th>Total Players</th>
                                        <th>Active Players</th>
                                        <th>Game Code</th>
                                        <th>Manager</th>
									</tr>
									</thead>
									<tbody>
									';

                foreach ($gamefetch as $rs) {

                    if ($status == 0 || $status == $rs['lms_game_status']) {

                        $rowcolor = 'black';
                        switch ($rs['lms_game_status']) {
                            case 1:
                                $rowcolor = 'blue';
                                break;
                            case 2:
                                $rowcolor = 'black';
                                break;
                            case 3:
                                $rowcolor = 'limegreen';
                                break;
                            case 4:
                                $rowcolor = 'silver';
                                break;
                        }
                        $html .= $key;
                        $html .= '
									<tr style="color:' . $rowcolor . '">
										<td><button class="gameselection"  type="submit" name="gameid" value="' . $rs['lms_game_id'] . '">' . $rs['lms_game_name'] . '</button></td>
										<td>' . sprintf('%02d', $rs['lms_week']) . '/' . $rs['lms_year'] . '</td>
                                        <td>' . $rs['lms_game_status_text'] . '</td>
                                        <td>' . $rs['lms_game_total_players'] . '</td>
                                        <td>' . $rs['lms_game_still_active'] . '</td>

                                        <td style="font-family:courier">' . $rs['lms_game_code'] . '</td>
<td>' . $rs['lms_player_screen_name'] . '</td>

									</tr>';
                    }
                }
                $html .= '
									</tbody>
								</table>
                            </form>
							</div>




                        </div>

			      		<div class="row">
							<div class="col-xs-6">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg" role="button">Back</a>
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
