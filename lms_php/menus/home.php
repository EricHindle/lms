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

$currentPage = 'home';

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

			<body>';
    include $myPath . 'globNAV.php';
    echo '

		      <div class="container" style="min-height:50vh;">
                <div  class="box" style="padding:1em;width:400px;margin:10px;">
                    <h2>Welcome, ' . $_SESSION['nickname'] . '</h2>
                    Match week: ';
    $html .= $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'];
    $html .= '      </br/>
                    Deadline for week &nbsp;' . sprintf('%02d', $_SESSION['currentweek'] + 1) . '&nbsp; picks is &nbsp;' . date_format(date_create($_SESSION['deadline']), 'd M Y');
    $html .= '  </div>';

    $html .= '	<div class="box" style="padding:1em;width:400px;margin:10px;">
                    <h3 class="" >Make a Pick for</h3>
                    <div class="" style="margin-left:16px;margin-right:16px">
                        <form class="" role="form" name ="editpick" method="post" action="' . $myPath . 'struct/picks/pick-main.php">';
    $html .= $key;

    foreach ($gamefetch as $myGame) {
        if ($myGame['lms_game_status'] < 3 && $myGame['lms_week'] <= $_SESSION['selectweek']) {
            $html .= '      <button class="gameselection graybutton"  type="submit" name="gameid" value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</button></br>';
        }
    }

    $html .= '          </form>
                    </div>
                </div>
                <div class="box" style="padding:1em;width:400px;margin:10px;">
                    <h3 class="" >Join a Game</h3>
                    <form role="form" name ="edit" method="post" action="' . $myPath . 'struct/game/process-join-game.php">';
    $html .= $key;
    $html .= '          <div>
                            <input type="text" style="padding-left:9px;"  id="gamecode" name="gamecode" placeholder="game code">
                        </div>
                        <div class="" style="margin-top:9px;">
                            <input  class="gameselection graybutton"  id="submit2" name="submit" type="submit" value="Submit" >
                        </div>
                    </form>
                </div>

                <div class="box" style="padding:1em;width:400px;margin:10px;">
                    <h3 class="">Show a Game</h3>
                    <div class="" style="margin-left:16px;margin-right:16px">
                        <form class="" role="form" name ="showgame" method="post" action="' . $myPath . 'struct/game/show-played-game.php">';
    $html .= $key;
    foreach ($gamefetch as $myGame) {
        $html .= '          <button class="gameselection graybutton"  type="submit" name="gameid" value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</button></br>';
    }
    $html .= '          </form>
                    </div>
                </div>

                <div class="box table-games" style="margin:10px;padding:1em;">
            		<h3>Games Played by ' . $_SESSION['nickname'] . '</h3>
				    <table class="center" style="color:black;background-color:white;" id="keywords">
                        <thead>
                            <tr class="">
								<th>Name</th>
								<th>Start Wk</th>
                                <th>Game Status</th>
                                <th>Total Players</th>
                                <th>Active Players</th>
                                <th>My Status</th>
                                <th>Current picks</th>
							</tr>
                        </thead>
                        <tbody>';

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
                $rowcolor = 'black';
                break;
            case 3:
                $rowcolor = 'green';
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
                if ($pickcount == 2) {
                    $newline = '</br>';
                }
                if ($matchcount > 0) {
                    $thispick = $matchweekpick['lms_team_name'] . ' (' . date_format(date_create($matchweekpick['lms_match_date']), 'd M Y') . ')';
                }
                if ($selectcount > 0) {
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
    								<tr style="color:' . $rowcolor . '">
    									<td>' . $rs['lms_game_name'] . '</td>
						         		<td>' . sprintf('%02d', $rs['lms_week']) . '/' . $rs['lms_year'] . '</td>    	
                                        <td>' . $rs['lms_game_status_text'] . '</td>
                                        <td>' . $rs['lms_game_total_players'] . '</td>
                                        <td>' . $rs['lms_game_still_active'] . '</td>
                                        <td style="color:' . $playercolor . '">' . $rs['lms_game_player_status_text'] . '</td>
                                        <td style="color:' . $selcolor . '">' . $currentpick . '</td>
    								</tr>';
    }
    $html .= '
                        </tbody>
                    </table>
                </div>
            </div>
        </body>
    </html>';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}

?> 

<?php $currentPage = 'home'; ?>

<div class="page-container">
	<a href="games.php" class="option">
		<h2>My Games</h2>
		<p>Make picks in your current games</p>
	</a> <a href="create.php" class="option">
		<h2>Create Game</h2>
		<p>Create a football knockout Game</p>
	</a> <a href="join.php" class="option">
		<h2>Join Game</h2>
		<p>Use a unique code to join a game</p>
	</a> <a href="manage.php" class="option">
		<h2>Manage</h2>
		<p>Manage games you’ve created</p>
	</a>
</div>
