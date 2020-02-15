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

    $playerchangesql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player ORDER BY lms_player_screen_name ASC";
    $playerchangequery = $mypdo->prepare($playerchangesql);
    $playerchangequery->execute();
    $cafetch = $playerchangequery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>Player Admin</title>
			    
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
		        <script>
					$(document).ready(function () {
						toggleFields();
						$("#role").change(function () {
							toggleFields();
						});
					});
				</script>
				<script>

					function validatePassReset(){
                            var canSubmit = false;
                            var password = $("#pwd1").val();
                            var confirmPassword = $("#pwd2").val();
                            if (password == confirmPassword) {
                            	if (password.length>7) {
                            		canSubmit = true;
                            	} else {
                            		alert("Password must contain at least 8 characters.");
                            		canSubmit = false;
                            	}
                            } else {
                                alert("Passwords do not match");
                            }

                            return canSubmit;
                    }

                    function checkPasswordMatch() {
                        var password = $("#pwd1").val();
                        var confirmPassword = $("#pwd2").val();
                        var element = document.getElementById("p1");
                        if (password.match(/[A-z]/) && password.match(/[A-Z]/) && password.match(/\d/) && password.length > 7)
                            document.getElementById("pwd1").style.borderColor = "green";
                        else
                            document.getElementById("pwd1").style.borderColor = "red";

                        if (password != confirmPassword)
                            document.getElementById("pwd2").style.borderColor = "red";
                        else {
                            document.getElementById("pwd2").style.borderColor = "green";
                            document.getElementById("pwd1").style.borderColor = "green";
                        }

                    }
                    $(document).ready(function () {
                        $("#pwd1, #pwd2").keyup(checkPasswordMatch);
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
			                <div class="col-md-9">
			                    <h1><strong>Player Admin</strong></h1>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			      		</div>
			        	<div class = "row">';

    $html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="addplayer" method="post" action="add-player.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Add Player</h3>
				                    <div class="form-group">
				                    	<label for="email">Email address :</label>
				                    	<input type="text" class="form-control" id="email" name="email" maxlength="100" placeholder="email" />
				                    	<label for="password">Password (no spaces or special characters):</label>
					                    <input type="text" class="form-control" id="password" name="password" placeholder="password" />
					                    <label for="fname">Forename (no special characters):</label>
					                    <input type="text" class="form-control" id="fname" name="fname" placeholder="forename" />
					                    <label for="sname">Surname (no special characters):</label>
					                    <input type="text" class="form-control" id="sname" name="sname" placeholder="surname" />
					                    <label for="screenname">Screen name (no special characters):</label>
					                    <input type="text" class="form-control" id="screenname" name="screenname" placeholder="screen name" />
					                    <input type="checkbox" name="isadmin" value="true"> Administrator<br>';
    $html .= '	                 
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="edituser" method="post" action="edit-player.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Edit Player</h3>
			                  
				                    <div class="form-group">
			                        	<label for="user">Choose Player:</label>
			                            <select class="form-control" id="user" name="user">';
    foreach ($cafetch as $myUser) {
        $html .= '<option value="' . $myUser['lms_player_id'] . '">' . $myUser['lms_player_screen_name'] . ' <small>(' . $myUser['lms_player_email'] . ')<small></option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group">
				                    
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="edituser" method="post" action="change-password.php" onsubmit="return validatePassReset()">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Change Password</h3>
									<h5>Password must contain at least 8 characters, including UPPERCASE, lowercase and numbers.</h5>
			                    	<br>
				                    <div class="form-group">
			                        	<label for="user">Choose Player:</label>
			                            <select class="form-control" id="user" name="user">';
    foreach ($cafetch as $myUser) {
        $html .= '<option value="' . $myUser['lms_player_id'] . '">' . $myUser['lms_player_screen_name'] . ' <small>(' . $myUser['lms_player_email'] . ')<small></option>';
    }
    $html .= '	                    </select>
									</div>
                                    <div class="form-group">
                                        <label for="pwd1">New password:</label>
                                        <input name="pwd1" id="pwd1" class="form-control" title="Password must contain at least 8 characters, including UPPERCASE, lowercase and numbers." type="password" onChange="checkPasswordMatch()">
                                    </div>
                                    <div class="form-group">
                                        <label for="pwd1">Confirm password:</label>
                                        <input id="pwd2" name="pwd2" class="form-control" title="Please enter the same Password as above." type="password" onChange="checkPasswordMatch()">
                                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '		</div>
						<div class = "row">
				        	<div class="well col-md-10 col-md-offset-1 col-sm-12  textDark">
				        		<h3>All Players</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Login</th>
										<th>Forename</th>
										<th>Surname</th>
										<th>Screen Name</th>
										<th>Access</th>
                                        <th>Active</th>
									</tr>
									</thead>
									<tbody>
									';
    foreach ($cafetch as $rs) {

        $active = ($rs['lms_active'] == 1 ? 'Yes' : 'No');
        $rowcolor = ($rs['lms_active'] == 1 ? 'black' : 'silver');
        $rowcolor = ($rs['lms_access'] > 900 ? 'blue' : $rowcolor);
        $html .= '
									<tr style="color:' . $rowcolor . '">
										<td>' . $rs['lms_player_login'] . '</td>
										<td>' . $rs['lms_player_forename'] . '</td>
										<td>' . $rs['lms_player_surname'] . '</td>
										<td>' . $rs['lms_player_screen_name'] . '</td>
										<td>' . $rs['lms_access'] . '</td>
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
