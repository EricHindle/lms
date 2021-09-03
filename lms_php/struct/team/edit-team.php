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
$currentPage = '';
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['team'])) {
                $teamid = sanitize_int($_POST['team']);
                if ($teamid) {

                    $html = "";
                    $teamsql = "SELECT lms_team_id, lms_team_name, lms_team_active, lms_team_abbr FROM lms_team WHERE lms_team_id = :id";
                    $teamquery = $mypdo->prepare($teamsql);
                    $teamquery->execute(array(
                        ':id' => $teamid
                    ));
                    $teamcount = $teamquery->rowCount();
                    if ($teamcount > 0) {
                        $leaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_league_team 
                                        JOIN lms_league ON lms_league_team_league_id = lms_league_id 
                                        WHERE lms_league_team_team_id = :teamid
                                        ORDER BY lms_league_id ASC";
                        $leaguequery = $mypdo->prepare($leaguesql);
                        $leaguequery->execute(array(
                            ':teamid' => $teamid
                        ));
                        $leaguefetch = $leaguequery->fetchAll(PDO::FETCH_ASSOC);
                        $allleaguesql = "SELECT lms_league_id, lms_league_name, lms_league_abbr FROM lms_league
                                            WHERE lms_league_supported = 1 
                                                AND lms_league_id NOT IN 
                                                (SELECT lms_league_team_league_id from lms_league_team where lms_league_team_team_id = :teamid)
                                            ORDER BY lms_league_id ASC";
                        $allleaguequery = $mypdo->prepare($allleaguesql);
                        $allleaguequery->execute(array(
                            ':teamid' => $teamid
                        ));
                        $allleaguefetch = $allleaguequery->fetchAll(PDO::FETCH_ASSOC);
                        $key = $formKey->outputKey();
                        $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
                        $isactive = "";
                        if ($teamfetch["lms_team_active"] == 1) {
                            $isactive = "checked";
                        }
                        echo '
		<!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Teams</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			</head>

			<body>';
        include $myPath . 'globNAV.php';
        $html .= '
                <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Edit Team</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <form role="form" name ="edit" method="post" action="process-edit-team.php">';
                        $html .= $key;
                        $html .= '
               	            <div class="form-group" style="padding:25px;text-align:left;">
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">Team Name:</label>' . $teamfetch['lms_team_name'] . '
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">Abbr:</label>' . $teamfetch['lms_team_abbr'] . '
                                </div>
                                <div>
                                    <label class="form-text" style="display:inline-block;width:30%;text-align:left">Status:</label>';
                        if ($teamfetch['lms_team_active'] == 1) {
                            $html .= 'Active';
                        } else {
                            $html .= 'Inactive';
                        }
        $html .= '              </div>
                            </div>
		                    <div class="form-group"  style="padding-left:10px;text-align:left;">
                                <label  >New name:</label>
                                <input type="text" class="form-field" id="teamname" name="teamname" value="' . $teamfetch['lms_team_name'] . '"><br>
                                <label class="form-text"  style="display:inline-block;width:40%;text-align:left">New abbr (3 chrs max):</label>    
                                <input type="text" class="form-field" id="teamabbr" name="teamabbr" value="' . $teamfetch['lms_team_abbr'] . '"><br>
                                <label class="form-text"  style="display:inline-block;width:30%;text-align:left"> </label>
                                <input type="checkbox"  name="isactive" id="isactive" value="true" ' . $isactive . ' >
                                <label style="color:#909090;" for="issupported">&nbsp is an Active Team</label>
                                <input type= "hidden" name= "id" value="' . $teamid . '" />
					        </div>
                            <div style="padding-top:25px;" >
                                <h4>Leagues for Team</h4>
                            </div>
                            <div class="form-group"  style="padding:10px;padding-bottom:20px  ;text-align:left;">
					        	<table class="table table-games table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Leagues</th>
                                        <th>Remove</th>
									</tr>
									</thead>
									<tbody>
									';
                        foreach ($leaguefetch as $rs) {
                            $html .= '
    									<tr>
    										<td>' . $rs['lms_league_name'] . '</td>
                                            <td><input type="checkbox" style="margin-left:20px;" name="rmv-' . $rs['lms_league_id'] . '" id="rmv-' . $rs['lms_league_id'] . '" value="true" ></td>
    									</tr>';
                        }
                        $html .= '
									</tbody>
								</table>
                            </div>
                        ';
                        if (! empty($allleaguefetch)) {
                            $html .= '                         
                            <div class="form-group">
                                <label for="addleague">&nbsp; Add League &nbsp;&nbsp;</label>
							    <input type= "checkbox" name= "addleague" id="addleague" value="true" />
							</div>	
				            <div class="form-group" >
	                            <select class="form-dropdown col-md-6 col-sm-6" style="width:70%" id="leagueid" name="leagueid">';
                            foreach ($allleaguefetch as $myLeague) {
                                $html .= ' <option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
                            }
                            $html .= '
    	                        </select>
                            </div>';
                        }
                        $html .= '							
                            <div class="form-group" style="padding-top:25px;margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>	                    
						</form>
                        <div class="light-text">
        		            <a href="' . $myPath . 'struct/team/team-main.php">Back</a>
				        </div>
		            </div>
		        </div>


			</body>
		</html>
		 ';
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='team-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='team-main.php';
									</script>";
                }
            } else {
                header('Location: ' . $myPath . 'index.php?error=1');
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>