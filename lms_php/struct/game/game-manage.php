<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {
    $gamesql = "SELECT lms_game_id, lms_game_start_wkno, lms_game_name, lms_game_code, lms_game_status_text, lms_game_total_players, lms_game_still_active, lms_week, lms_year FROM v_lms_game WHERE lms_game_manager = :manager  ORDER BY lms_game_start_wkno, lms_game_name";
    $gamequery = $mypdo->prepare($gamesql);
    $gamequery->bindParam(":manager", $_SESSION['user_id'], PDO::PARAM_INT);
    $gamequery->execute();
    $gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);

    $remainingweeks = get_remaining_weeks(false);

    $mygamessql = "SELECT lms_game_id, lms_game_id, lms_game_name FROM v_lms_player_games WHERE lms_player_id = :player and  ORDER BY lms_game_name";
    $mygamesquery = $mypdo->prepare($mygamessql);
    $mygamesquery->bindParam(":player", $_SESSION['user_id'], PDO::PARAM_INT);
    $mygamesquery->execute();
    $mygamesfetch = $mygamesquery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";
    $key = $formKey->outputKey();
    echo '
      <!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
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
								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			            </div>
                        <div class="row">


			            	<div class="well col-md-10  textDark">
				        		<h3>Games Managed by ' . $_SESSION['nickname'] . '</h3>
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
        if ($rs['lms_game_status'] > 2) {
            $rowcolor = 'silver';
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
                        <div class="row">
			                <div class="col-sm-4">
			                    <div class="tile green">
			                	<form class="form-horizontal" role="form" name ="showgame" method="post" action="show-game.php">';
    $html .= $key;
    $html .= '					<h3 class="title">Show a Game</h3>
				                    <div class="form-group" style="margin-left:16px;margin-right:16px">
			                            <select class="form-control" id="gameid" name="gameid">';
    foreach ($gamefetch as $myGame) {
        $html .= '<option value="' . $myGame['lms_game_id'] . '">' . $myGame['lms_game_name'] . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group" style="margin-left:16px;margin-right:16px">
                                        <br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
                                    </form>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile teal">
                                    <h3 class="title" >Create a Game</h3>
                                    <form class="form-horizontal" role="form" name ="addteam" method="post" action="add-game.php">';
    $html .= $key;
    $html .= '					         <div class="form-group" style="margin-left:16px;margin-right:16px">
				                    	       <label for="gamename">Game Name:</label>
					                           <input type="text" class="form-control" id="gamename" name="gamename" placeholder="Game name" />
				                         </div>
                                         <div class="form-group" style="margin-left:16px;margin-right:16px">
                                               <label for="gamestartweek">Start week:</label>
                                               <select class="form-control" id="gamestartweek" name="gamestartweek">';
    foreach ($remainingweeks as $wk) {
        $html .= '<option value="' . $wk['lms_week_no'] . '">' . sprintf('%02d', $wk['lms_week']) . ' : ' . date_format(date_create($wk['lms_week_start']), 'd-M-Y') . '</option>';
    }
    $html .= '	                               </select>
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
							<div class="col-xs-6">
								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-lg" role="button">Back</a>
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
