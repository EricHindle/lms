<?php
$myPath = '../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
$playerid = $_SESSION['user_id'];
$gamesql = "SELECT * FROM v_lms_player_games WHERE lms_player_id = :player ORDER BY lms_game_player_status, lms_game_name ASC";
$gamequery = $mypdo->prepare($gamesql);
$gamequery->bindParam(':player', $playerid, PDO::PARAM_INT);
$gamequery->execute();
$gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
$currentPage = 'games';
$picksql = "SELECT lms_team_name, lms_match_date  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
$key = $formKey->outputKey();
$html = '';
if (login_check($mypdo) == true) {
    echo '
<!doctype html>
<html>
	<head> 
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="' . $myPath . 'css/style.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    </head>
	<body>          
            ';
    include $myPath . 'globNAV.php';
    echo '
<div class="page-container">

    
<div class="game-list-container">

        ';
            foreach ($gamefetch as $rs) {
                $gameid = $rs['lms_game_id'];
                $pickquery = $mypdo->prepare($picksql);
                $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
                $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
                $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
                $pickquery->execute();
                $matchcount = $pickquery->rowCount();
                $matchweekpick = $pickquery->fetch(PDO::FETCH_ASSOC);
                $pickquery->bindParam(':matchwk', $_SESSION['selectweekkey']);
                $pickquery->execute();
                $selectweekpick = $pickquery->fetch(PDO::FETCH_ASSOC);
                $selectcount = $pickquery->rowCount();
                $pickcount = $matchcount + $selectcount;
                $currentpick = '';
                $thispick = '';
                $nextpick = '';
                $rowcolor = 'black';
                $selcolor = 'black';
                $playercolor = 'black';
                switch ($rs['lms_game_status']) {
                    case 1:
                        $rowcolor = 'blue';
                        break;
                    case 2:
                        $rowcolor = 'status-waiting';
                        break;
                    case 3:
                        $rowcolor = 'status-out';
                        break;
                    case 4:
                        $rowcolor = 'silver';
                        break;
                }
                if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3) {
                    $playercolor = $rs['lms_game_player_status'] == 2 ? 'red' : 'silver';
                } else {
                    if ($pickcount > 0) {
                        $newline = '';
                        if ($pickcount == 2){
                            $newline = '</br>';
                        }
                        if ($matchcount > 0){
                        $thispick = $matchweekpick['lms_team_name'] . ' (' . date_format(date_create($matchweekpick['lms_match_date']), 'd M Y') . ')';
                        }
                        if ($selectcount > 0){
                        $nextpick = $selectweekpick['lms_team_name'] . ' (' . date_format(date_create($selectweekpick['lms_match_date']), 'd M Y') . ')';
                        }
                        $currentpick = $thispick . $newline . $nextpick;             
                    } else {
                        if ($rs['lms_game_start_wkno'] <= $_SESSION['matchweek']) {
                            $currentpick = '(waiting)';
                            $selcolor = 'crimson';
                        }
                    }
                }
                $html .= '
            
                <form class="" role="form" name ="showgame" method="post" action="' . $myPath . 'struct/game/show-played-game.php">';
                $html .= $key;
                $html .= '<button style="width:100%" type="submit" name="gameid" value="' . $rs['lms_game_id'] . '">
                
                
                <table class="table-games">
                    <tr class="' . $rowcolor . '">
                        <td class="table-gameName">
                            <div class="table-columnTitle">Game Name:</div>
                            <div>' . $rs['lms_game_name'] . '</div>
                        </td>  	
                        <td>
                            <div class="table-columnTitle">Game Status:</div>
                            <div>' . $rs['lms_game_status_text'] . '</div>
                        </td>
                        <td>
                            <div class="table-columnTitle">Players:</div>
                            <div>' . $rs['lms_game_still_active'] . ' / ' . $rs['lms_game_total_players'] . '</div>
                        </td>
                        <td>
                            <div class="table-columnTitle">Your Status:</div>
                            <div>' . $rs['lms_game_player_status_text'] . '</div>
                        </td>
                        <td>
                            <div class="table-columnTitle">Your pick:</div>
                            <div>' . $currentpick . '</div>
                        </td>
                        <td>
                            <div>></div>
                        </td>
                    </tr>
                </table>
            </button>
            </form>
            
            ';
            }
            $html .= '

    </div>

    <div class="join-game-container">
        <div class="join-game-box">
            <div class="join-game-title">
                <h2>Join a Game</h2>
                <p>Enter a code to join an already existing game.</p>
            </div>
            <form class="join-game-form" role="form" name ="edit" method="post" action="' . $myPath . 'struct/game/process-join-game.php">';
                $html .= $key;
                $html .= '
                <input type="text" class="form-field" style="background-color:#f2f2f2;margin-bottom:0;" id="gamecode" name="gamecode" placeholder="Enter Game Code">
                <button type="submit" name="submit" id="submit" value="Submit" class="btn" style="border-radius:0px 20px 20px 0px">Join Game</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
    	';
    echo $html;
} 
else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?> 
