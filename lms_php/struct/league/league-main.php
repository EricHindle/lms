<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();

    $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr, lms_league_supported FROM lms_league ORDER BY lms_league_id ASC";
    $leaguequery = $mypdo->prepare($leaguesql);
    $leaguequery->execute();
    $leaguefetch = $leaguequery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>League Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
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
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-6 col-sm-8">
			                    <h1><strong>League Admin</strong></h1>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			      		</div>
			        	<div class = "row">';

    $html .= '			<div class="well col-md-3 col-sm-4  textDark">
			                	<form class="form" role="form" name ="addleague" method="post" action="add-league.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Add League</h3>
				                    <div class="form-group">
				                    	<label for="leaguename">League Name:</label>
					                    <input type="text" class="form-control" id="leaguename" name="leaguename" placeholder="League name" />
				                    </div>
					                <div class="form-group">
				                    	<label for="leagueabbr">Abbreviation:</label>
					                    <input type="text" class="form-control" id="leagueabbr" name="leagueabbr" placeholder="Abbr" />
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '			<div class="well col-md-3 col-sm-4 col-md-offset-1 col-sm-offset-1 textDark">
			                	<form class="form" role="form" name ="editleague" method="post" action="edit-league.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Edit League</h3>
				                    <div class="form-group">
			                        	<label for="user">Choose League:</label>
			                            <select class="form-control" id="league" name="league">';
    foreach ($leaguefetch as $myLeague) {
        $html .= '<option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '		</div>
						<div class = "row">
				        	<div class="well col-md-7 col-sm-9 textDark">
				        		<h3>All Leagues</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Name</th>
                                        <th>Abbr</th>
										<th>Supported</th>
									</tr>
									</thead>
									<tbody>
									';
    foreach ($leaguefetch as $rs) {
        $supported = ($rs['lms_league_supported'] == 1 ? 'Yes' : 'No');
        $html .= '
									<tr>
										<td>' . $rs['lms_league_name'] . '</td>
                                        <td>' . $rs['lms_league_abbr'] . '</td>
										<td>' . $supported . '</td>
									</tr>';
    }
    $html .= '
									</tbody>
								</table>
							</div>
						</div>

				        ';

    $html .= '	      		
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg" role="button">Back</a>
								<br>
							</div>
						</div>
			      		<br><br><br><br>
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
