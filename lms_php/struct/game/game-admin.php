<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
$currentPage = '';
$formKey = new formKey();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $status = 0;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                $statustext = ucfirst(get_game_status($status));
                if ($statustext == 'Code missing') {
                    $statustext = "All";
                }
            }
        }
    }

    $gamesql = "SELECT * FROM v_lms_game ORDER BY lms_game_status, lms_game_start_wkno, lms_game_name";
    $gamefetch = get_games_for_current_user($gamesql);

    $html = "";
    $key = $formKey->outputKey();
    echo '
      <!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Games</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
			    <script>
		            $(function(){
		            $(\'#keywords\').tablesorter(); 
		            });
		        </script>
			</head>
			<body>';

    include $myPath . 'globNAV.php';
    $html .= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-8">
			                    <h1><strong>' . $statustext . ' Games</strong></h1>
			                </div>
			      		</div>
                        <div class="box" style="padding:1em;text-align:left">
    			            <form class="form-horizontal" role="form" name ="showgame" method="post" action="show-game-admin.php">
        			        	<table class="table table-bordered" id="keywords">
        							<thead>
    									<tr class="game">
    										<th>Name</th>
    										<th>Start Wk</th>
                                            <th>Game Status</th>
                                            <th>Game Code</th>
                                            <th>Manager</th>
    									</tr>
        						    </thead>
        						    <tbody>';
    foreach ($gamefetch as $rs) {
        if ($status == 0 || $status == $rs['lms_game_status']) {
            $rowcolor = 'black';
            switch ($rs['lms_game_status']) {
                case 1:
                    $rowcolor = 'lightblue';
                    break;
                case 2:
                    $rowcolor = 'lightyellow';
                    break;
                case 3:
                    $rowcolor = 'lightgreen';
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
                    <div style="padding:2em;">
                        <a href="' . $myPath . 'struct/main.php" class="btn" style="padding:15px;" role="button">Back</a>
                    </div>
                </div>
            </section>
		</body>
	</html>        ';

    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
