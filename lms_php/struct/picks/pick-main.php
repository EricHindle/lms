<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
$formKey = new formKey();
if (login_check($mypdo) == true) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['gameid'])) {
                $id = sanitize_int($_POST['gameid']);
                if ($id) {
                    $player = $_SESSION['user_id'];
                    $gamename = get_game_name($id);
                    $gps = get_game_player_status($id, $player);

                    $picksql = "SELECT lms_match_result,lms_week, lms_year,lms_match_date,lms_team_name FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game";
                    $pickquery = $mypdo->prepare($picksql);
                    $pickquery->bindParam(':player', $player, PDO::PARAM_INT);
                    $pickquery->bindParam(':game', $id, PDO::PARAM_INT);
                    $pickquery->execute();
                    $pickfetch = $pickquery->fetchAll(PDO::FETCH_ASSOC);
                    
                   
                    $html = "";
                    $key = $formKey->outputKey();
                    echo '
                          <!doctype html>
                    		<html>
                    			<head>
                    				
                    			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
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
                    $html .= '
                    				<section id="homeSection">
                    			    <br><br>
                    			        <div class="container">
                    			            <div class="row">
                    			                <div class="col-md-12">
                    			                    <h1><strong>Selections</strong></h1>
                    			                    <br>
                    			                </div>
                    			            </div>
                        <div class="row">
			            	<div class="well col-md-8  textDark">
				        		<h3>' . $_SESSION['nickname'] . ' Selections for ' . $gamename . '</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="game">
										<th>Match Week</th>
                                        <th>Match Date</th>
                                        <th>Team</th>
                                        <th>Outcome</th>
									</tr>
									</thead>
									<tbody>
									';

                    foreach ($pickfetch as $rs) {
                        $result = 'no result';
                        switch ($rs['lms_match_result']) {
                            case 'w':
                                $result = 'win';
                                break;
                            case 'l':
                                $result = 'lose';
                                break;
                            case 'd':
                                $result = 'draw';
                                break;
                        }

                        $html .= '
									<tr>
										<td>' . $rs['lms_week'] . '/' . $rs['lms_year'] . '</td>
                                        <td>' . date_format(date_create($rs['lms_match_date']), 'd-M-Y') . '</td>
                                        <td>' . $rs['lms_team_name'] . '</td>
                                        <td>' . $result . '</td>
									</tr>';
                    }
                    $html .= '
									</tbody>
								</table>
							</div>
						</div>';
          
                    if ($gps['lms_game_player_status'] == "1") {
                        $html .= '    <div class="row">
			            	<div class="col-sm-6">
			                    <div class="tile red">
		                    		    <h3 class="title" >Select a Team for This Week</h3>
					                	
			                	<form class="form-horizontal" role="form" name ="editpick" method="post" action="edit-pick.php">';
                        $html .= $key;
                        $html .= '
				                    <div class="col-sm-9">
			                            <select class="form-control" id="pickid" name="pickid">';
                        foreach ($pickfetch as $mypick) {
                            $html .= '<option value="' . $mypick['lms_pick_id'] . '">' . $mypick['lms_pick_name'] . '</option>';
                        }
                        $html .= '	                    </select>
				                    </div>
				                    <div class="col-sm-2 col-sm-offset-1">
                                        <br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
                                    </form>
			          			</div>
			                </div>
                        </div>';
                    }
                    $html .= '		<div class="row">
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
                } else {
                    $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='pick-main.php';
									</script>";
                }

                echo $html;
            } else {
                echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='pick-main.php';
									</script>";
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
