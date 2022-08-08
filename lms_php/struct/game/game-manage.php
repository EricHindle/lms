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
$currentPage = 'manage';
$formKey = new formKey();
if (login_check($mypdo) == true) {
    $gamesql = "SELECT lms_game_id, lms_game_start_wkno, lms_game_name, lms_game_code, lms_game_status, lms_game_status_text, 
                lms_game_total_players, lms_game_still_active, lms_week, lms_year FROM v_lms_game WHERE lms_game_manager = :manager  
                ORDER BY lms_game_start_wkno DESC, lms_game_name";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(":manager", $_SESSION['user_id'], PDO::PARAM_INT);
    $gamequery->execute();
    $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);

    $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_league WHERE lms_league_supported = 1 ORDER BY lms_league_id ASC";
    $leaguequery = $mypdo->prepare($leaguesql);
    $leaguequery->execute();
    $leaguefetch = $leaguequery->fetchAll(PDO::FETCH_ASSOC);

    $remainingweeks = get_remaining_weeks(false);

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
                <div class="container">
        <div  class="box" style="margin-top:20px;">
                        <h2>Manage My Games</h2>
            <p>Add/remove leagues, Change game name, Change game start week, Cancel the game</p>
                    </div>

    
    
    <form class="game-card-form" role="form" name ="showgame" method="post" action="show-game.php">';

    $html .= $key;

    $html .= '';		 

    foreach ($gamefetch as $myGame) {
        if ($myGame['lms_game_status'] < 3) {
    $html .= '	                   
            
            <div class="game-card"  style="margin-bottom:20px">
            <button style="width:100%" class="game-button" type="submit" name="gameid" value="' . $myGame['lms_game_id'] . '">
 
                <table class="game-table" style="padding-bottom:3em">
                    <tr>
                        <th colspan="2" border="0">
                        <div><h2>' . $myGame['lms_game_name'] . '</h2></div>
                        <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                        </th>
							</tr>
                    <tr>
                        <td width="50%">
                        <div class="table-columnTitle">Game Week</div>
                        <div>' . sprintf('%02d', $myGame['lms_week']) . '</div>
                        </td>
                        <td width="50%">
                        <div class="table-columnTitle">Game Status:</div>
                        <div>' . $myGame['lms_game_status_text'] . '</div>
                        </td>
                        <td>
                        <div class="table-columnTitle">Players</div>
                        <div>' . $myGame['lms_game_still_active'] . ' / ' . $myGame['lms_game_total_players'] . '</div>
                        </td>
                    </tr>

                    <tr>
                    <td colspan="2" >
                        <div class="table-columnTitle">Game Code:</div>
                        <div class="game-code">' . $myGame['lms_game_code'] . '</div>'; $html .= '          </td>
                    <td>
                    <img style="width:30px" src="' . $myPath . 'img/icons/PickButton.svg">
                    </td>
                </tr>
                </table>
            </button>
            </div>
            
            
									';
        }
    }
    $html .= '

            </form>

		</body>
	</html>
		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
