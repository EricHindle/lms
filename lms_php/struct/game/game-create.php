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

$currentPage = 'create';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    /*
     * $gamesql = "SELECT lms_game_id, lms_game_start_wkno, lms_game_name, lms_game_code, lms_game_status, lms_game_status_text, lms_game_total_players, lms_game_still_active, lms_week, lms_year FROM v_lms_game WHERE lms_game_manager = :manager ORDER BY lms_game_start_wkno, lms_game_name";
     * $gamequery = $mypdo->prepare($gamesql);
     * $gamequery->bindParam(":manager", $_SESSION['user_id'], PDO::PARAM_INT);
     * $gamequery->execute();
     * $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
     */

    $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_league WHERE lms_league_supported = 1 ORDER BY lms_league_id ASC";
    $leaguequery = $mypdo->prepare($leaguesql);
    $leaguequery->execute();
    $leaguefetch = $leaguequery->fetchAll(PDO::FETCH_ASSOC);

    // $remainingweeks = get_remaining_weeks(false,1);

    $html = "";
    $key = $formKey->outputKey();
    echo '
      <!doctype html>
		<html>
			<head>
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">

			    <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    
			    <title>Create Game</title>   
			</head>
            <body>';
    include $myPath . 'globNAV.php';
    $html .= '
                <div class="page-container">
                    <div class="box">
                        <h2>Create a Game</h2><br>
                        <form class="form-horizontal" role="form" name ="addteam" method="post" action="add-game.php">';
    $html .= $key;
    $html .= '
     
                            <input type="text" class="form-field" id="gamename" name="gamename" placeholder="Game name" />';

    /*
     * <select class="form-dropdown" id="gamestartweek" name="gamestartweek">
     * <option value="" disabled selected>Select Starting Week</option>';
     * foreach ($remainingweeks as $wk) {
     * $html .= '<option value="' . $wk['lms_week_no'] . '">' . sprintf('%02d', $wk['lms_week']) . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
     * }
     * $html .= '
     * </select>
     */

    $html .= '              <select class="form-dropdown" id="leagueid" name="leagueid">
                                <option value="" disabled selected>Select League</option>';
    foreach ($leaguefetch as $myLeague) {
        $html .= '              <option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
    }
    $html .= '	        
                            </select>
                            Number of times each team can be picked :
                            <select class="form-dropdown" id="pickcount" name="pickcount" style="width: 25%;padding:10px;">';
                            for ($i = 1; $i <= 10; $i++){
                                $html .= '<option value='.$i.'>'.$i.'</option>';	   
                            }
   
    $html .= '           
                            </select>
	                        <button type="submit" name="submit" id="submit" value="Submit" class="btn">Create</button>
                        </form>
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