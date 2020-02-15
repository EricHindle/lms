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

$picksql = "SELECT lms_team_name, lms_match_date  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";

$key = $formKey->outputKey();
$html = '';
if (login_check($mypdo) == true) {
    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>Home</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			</head>

			<body>';
    include $myPath . 'globNAV.php';
    echo '
				<section id="homeSection">
			    <br><br>
		        <div class="container">
		            <div class="row">
		                <div class="col-md-10">
		                    <h1><strong>Welcome ' . $_SESSION['nickname'] . '</strong></h1>
		                    <br>
		                </div>
		            </div>';

    $html .= '      <div class="row">
		            	<div class="well col-md-10  textDark">
			        		<h3>Games Played by ' . $_SESSION['nickname'] . '</h3>
				        	<table class="table table-bordered" id="keywords">
								<thead>
    								<tr class="game">
    									<th>Name</th>
    									<th>Start Wk</th>
                                        <th>Game Status</th>
                                        <th>Total Players</th>
                                        <th>Active Players</th>
                                        <th>My Status</th>
                                        <th>Current Selection</th>
    								</tr>
								</thead>
								<tbody>
									';

    foreach ($gamefetch as $rs) {
        $gameid = $rs['lms_game_id'];
        $pickquery = $mypdo->prepare($picksql);
        $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
        $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
        $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
        $pickquery->execute();
        $pickcount = $pickquery->rowCount();
        // $pickfetch = $pickquery->fetch(PDO::FETCH_ASSOC);

        $currentpick = '';
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
                $rowcolor = 'limegreen';
                break;
            case 4:
                $rowcolor = 'silver';
                break;
        }
        if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3) {
            $playercolor = $rs['lms_game_player_status'] == 2 ? 'red' : 'silver';
        } else {
            if ($pickcount > 0) {
                $pickfetch = $pickquery->fetch(PDO::FETCH_ASSOC);
                $currentpick = $pickfetch['lms_team_name'] . ' (' . date_format(date_create($pickfetch['lms_match_date']), 'd M Y') . ')';
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

		            <div class="row">
		            	<div class="col-sm-3 col-md-3">
		                    <div class="tile red">
		                    		<h3 class="title" >Selections for</h3>
                                <form class="form-horizontal" role="form" name ="editpick" method="post" action="' . $myPath . 'struct/picks/pick-main.php">';
    $html .= $key;
    $html .= '					
		                            <div class="form-group" style="margin-left:16px;margin-right:16px">
	                                     <select class="form-control" id="gameid" name="gameid">';
    foreach ($gamefetch as $myGame) {
        if ($myGame['lms_game_start_wkno'] <= $_SESSION['matchweek']) {
            $html .= '<option value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</option>';
        }
    }
    $html .= '	                         </select>
		                            </div>
		                            <div class="form-group" style="margin-left:16px;margin-right:16px">
                                        <br>
		                                <input id="submit1" name="submit" type="submit" value="Submit" class="btn btn-primary">
		                            </div>
                                </form>
                            </div>
		                </div>
		            	<div class="col-sm-3 col-md-3">
		                    <div class="tile orange">
	                    		    <h3 class="title" >Join a Game</h3>
				                	<form role="form" name ="edit" method="post" action="' . $myPath . 'struct/game/process-join-game.php">';
    $html .= $key;
    $html .= '                          <div class="form-group " style="margin-left:16px;margin-right:16px">
					                        <input type="text" class="form-control" id="gamecode" name="gamecode" placeholder="game code">
					                    </div>
					                    <div class="form-group" style="margin-left:16px;margin-right:16px">
					                    	<br>
					                        <input id="submit2" name="submit" type="submit" value="Submit" class="btn btn-primary">
					                    </div>
					                </form>
			          			</div>
			                </div>
    		                <div class="col-sm-3 col-md-3">
		                      <div class="tile green">
		                	     <form class="form-horizontal" role="form" name ="showgame" method="post" action="' . $myPath . 'struct/game/show-played-game.php">';
    $html .= $key;
    $html .= '					     <h3 class="title">Show a Game</h3>
			                         <div class="form-group" style="margin-left:16px;margin-right:16px">
		                                  <select class="form-control" id="gameid" name="gameid">';
    foreach ($gamefetch as $myGame) {
        $html .= '<option value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</option>';
    }
    $html .= '	                             </select>
			                         </div>
			                     <div class="form-group" style="margin-left:16px;margin-right:16px">
                                    <br>
			                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
			                    </div>
                            </form>
	          			</div>
	                </div>
	      		</div>
                <div class="row">
	                <div class="col-sm-8">
	                    <div class="tile teal">
	                    	<a href="' . $myPath . 'struct/game/game-manage.php">
	                    		<h3 class="title" style="text-align:center">Manage Games</h3>
	                        </a>
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
