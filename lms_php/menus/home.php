<?php
	$myPath='../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start();
	if (isset($_SESSION['svid'])) {
		unset($_SESSION['svid']);
	}
	if (isset($_SESSION['svsec'])) {
		unset($_SESSION['svsec']);
	}
	$matchwk = $_SESSION['currentseason'].$_SESSION['currentweek'];
	$picksql = "SELECT lms_team_name FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
	$gamesql = "SELECT * FROM v_lms_player_games WHERE lms_player_id = :player ORDER BY lms_game_name ASC";
	$gamequery = $mypdo->prepare($gamesql);
	$gamequery->bindParam(':player', $_SESSION['user_id'],PDO::PARAM_INT);
	$gamequery->execute();
	$gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
	$html = '';
	if(login_check($mypdo) == true) {
		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Home</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
			    <script src="'.$myPath.'js/jquery.js"></script>
			    <script src="'.$myPath.'js/bootstrap.min.js"></script>
			</head>

			<body>';
				include $myPath.'globNAV.php';
		echo '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Welcome '. $_SESSION['nickname']. '</strong></h1>
			                    <br>
			                </div>
			            </div>';
								
			$html .='            <div class="row">
			            	<div class="well col-md-10  textDark">
				        		<h3>'. $_SESSION['nickname']. ' Games</h3>
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
							    
							    $pickquery = $mypdo->prepare($picksql);
							    $pickquery->bindParam(':player', $_SESSION['user_id'],PDO::PARAM_INT);
							    $pickquery->bindParam(':game', $rs['lms_game_id'],PDO::PARAM_INT);
							    $pickquery->bindParam(':matchwk',$matchwk);
							    $pickquery->execute();
							    if($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3){
    							    $currentpick = '';
							    }else{
    							    if ($pickquery->rowCount() > 0){
    							        $pickfetch = $pickquery->fetch(PDO::FETCH_ASSOC);
    							        $currentpick = $pickfetch['lms_team_name'];
    							    }else{
    							        $currentpick = '(waiting)';
    							    }
							    }
								$html .='
									<tr>
										<td>' . $rs['lms_game_name'] . '</td>
										<td>' . $rs['lms_game_start_wkno'] . '</td>
                                        <td>' . $rs['lms_game_status_text'] . '</td>
                                        <td>' . $rs['lms_game_total_players'] . '</td>
                                        <td>' . $rs['lms_game_still_active'] . '</td>
                                        <td>' . $rs['lms_game_player_status_text'] . '</td>
                                        <td>' . $currentpick . '</td>
									</tr>';
							}
							$html .='
									</tbody>
								</table>
							</div>
						</div>

			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
			                    	<a href="'.$myPath.'struct/picks/pick-main.php">
			                    		<h3 class="title" >Team Selections</h3>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
			                    	<a href="'.$myPath.'struct/game/game-main.php">
			                    		<h3 class="title" >Games</h3>
			                        </a>	
			          			</div>
			                </div>
			      		</div>
		    	</div>
		    </section>
		</body>
	</html>

		';
							echo $html;
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}

?>
