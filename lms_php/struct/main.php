<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020-21 Eric Hindle. All rights reserved.
 */
$myPath = '../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
$currentPage = '';
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
 			    <meta charset="UTF-8">
			    <title>LML Administration</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
        	</head>
			<body>';
    include $myPath . 'globNAV.php';
    $html .= '
                <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Administration</h2>
                    </div>
                    <div class="box" style="padding:1em;padding-left:10%;padding-right:10%;margin:10px;">
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'struct/player/player-main.php">
                                <h3 style="color:white;">Players</h3>
                            </a>
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'struct/week/week-main.php">
                                <h3 style="color:white;">Periods</h3>
                            </a>
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'struct/team/team-main.php">
                                <h3 style="color:white;" >Teams</h3>
                            </a>
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'struct/league/league-main.php">
                                <h3 style="color:white;">Leagues</h3>
                            </a>
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'struct/info/info-main.php">
                                <h3 style="color:white;" >Settings</h3>
                            </a>
                        </div>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Matches for :</h3>
                        <form role="form" name ="matchmain" method="post" action="' . $myPath . 'struct/match/match-main.php">';
    $html .= $key;
    $html .= '              <div class="form-group " style="margin-left:16px;margin-right:16px">
                                <input type="text" class="form-control" id="matchperiod" name="matchperiod" placeholder="yyyyww">
					        </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="margin:10px;padding:5px;width:50%;">
					        </div>
					    </form>
			        </div>
                    <div class="box" style="padding:1em;padding-left:10%;padding-right:10%;margin:10px;">
                        <h3>Games</h3>
                        <form role="form" name ="gameadmin" method="post" action="' . $myPath . 'struct/game/game-admin.php">';
    $html .= $key;
    $html .= ' 
                                <button class="btn graybutton" style="margin:3px;padding:3px;width:100%;" type="submit" name="status" value="0">All</button>';
    foreach ($statuslist as $status) {
        $html .= '              <button class="btn graybutton" style="margin:3px;padding:3px;width:100%;" type="submit" name="status" value="' . $status['lms_game_status_id'] . '">' . $status['lms_game_status_text']  . '</button>';
    }
    $html .= '	                    
				        </form>
			        </div>
            	</div>
            </body>
        </html>';
    echo $html;
    
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
