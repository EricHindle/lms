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
                    <div class="box" style="padding:1em;">
                        <h2>Manage My Games</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3 class="title">Choose a game to change</h3>
                        <form role="form" name ="showgame" method="post" action="show-game.php">';
    $html .= $key;
    $html .= '					
				            <div class="form-group" style="margin-left:4px;margin-right:4px;">
	';		                            
    foreach ($gamefetch as $myGame) {
        if ($myGame['lms_game_status'] < 3) {
            $html .= '              <button class="gameselection graybutton"  type="submit" name="gameid" value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</button></br>';
        }
    }
    $html .= '	                   
		                    </div>
                        </form>
                        <div class="form-text">
                          Add/remove leagues : Change game name<br />Change game start week : Cancel the game
                        </div>
          			</div>
                    <div class="box" style="padding:1em;text-align:left">
		        		<h4>Games Managed by ' . $_SESSION['nickname'] . '</h4>
			        	<table class="table table-bordered" id="keywords">
							<thead>
							<tr class="game">
								<th>Name</th>
								<th>Start Wk</th>
                                <th>Game Status</th>
                                <th>Total Players</th>
                                <th>Active Players</th>
                                <th>Game Code</th>
							</tr>
						</thead>
						<tbody>
									';
    foreach ($gamefetch as $rs) {
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
    
        $html .= '
                            <tr style="color:' . $rowcolor . '">
                            	<td>' . $rs['lms_game_name'] . '</td>
                            	<td>' . sprintf('%02d', $rs['lms_week']) . '/' . $rs['lms_year'] . '</td>
                                <td>' . $rs['lms_game_status_text'] . '</td>
                                <td>' . $rs['lms_game_total_players'] . '</td>
                                <td>' . $rs['lms_game_still_active'] . '</td>
                                <td style="font-family:courier">' . $rs['lms_game_code'] . '</td>
                            </tr>';
    }
    $html .= '
                		</tbody>
                	</table>
                </div>
            </div>
		</body>
	</html>
		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
