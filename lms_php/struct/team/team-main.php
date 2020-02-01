<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();

    $teamsql = "SELECT lms_team_id, lms_team_name, lms_team_active FROM lms_team ORDER BY lms_team_name ASC";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->execute();
    $teamfetch = $teamquery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Team Admin</title>
			    
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
			                <div class="col-md-11">
			                    <h1><strong>Team Admin</strong></h1>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			      		</div>
			        	<div class = "row">';

    $html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="addteam" method="post" action="add-team.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Add Team</h3>
				                    <div class="form-group">
				                    	<label for="teamname">Team Name:</label>
					                    <input type="text" class="form-control" id="teamname" name="teamname" placeholder="Team name" />
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="editteam" method="post" action="edit-team.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Edit Team</h3>
				                    <div class="form-group">
			                        	<label for="user">Choose Team:</label>
			                            <select class="form-control" id="team" name="team">';
    foreach ($teamfetch as $myTeam) {
        $html .= '<option value="' . $myTeam['lms_team_id'] . '">' . $myTeam['lms_team_name'] . '</option>';
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
				        	<div class="well col-md-7 col-md-offset-1 textDark">
				        		<h3>All Teams</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Name</th>
										<th>Active</th>
									</tr>
									</thead>
									<tbody>
									';
    foreach ($teamfetch as $rs) {
        if ($rs['lms_team_active'] == 1) {
            $active = 'Yes';
        } else {
            $active = 'No';
        }
        $html .= '
									<tr>
										<td>' . $rs['lms_team_name'] . '</td>
										<td>' . $active . '</td>
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
