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
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();

    $playerchangesql = "SELECT 
                        lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_player_mobile, lms_access, lms_active, lms_player_email_verified 
                        FROM lms_player 
                        ORDER BY lms_player_screen_name ASC";
    $playerchangequery = $mypdo->prepare($playerchangesql);
    $playerchangequery->execute();
    $cafetch = $playerchangequery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta charset="UTF-8">
			    <title>Player Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />

			    <script src="' . $myPath . 'js/jquery.js"></script>
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
<div class="container">
        <div class="box"  style="padding:1em;">
            <h2>Player Admin</h2>
        </div>

        <div class="box" style="padding:1em;padding-left:2%;padding-right:2%;margin:10px;">
            <h3 class="text-center">New Player</h3>
            <form class="form-horizontal" role="form" name ="addplayer" method="post" action="add-player.php">';
    $html .= $key;
    $html .= '
                <div class="form-group">
                	<input type="text" class="form-field" style="padding:10px" id="email" name="email" maxlength="100" placeholder="email" /><br/>
                    <input type="text" class="form-field" style="padding:10px" id="password" name="password" placeholder="password" /><br/>
                    <input type="text" class="form-field" style="padding:10px" id="fname" name="fname" placeholder="forename" /><br/>
                    <input type="text" class="form-field" style="padding:10px" id="sname" name="sname" placeholder="surname" /><br/>
                    <input type="text" class="form-field" style="padding:10px" id="mobile" name="mobile" placeholder="phone number" /><br/>
                    <input type="text" class="form-field" style="padding:10px" id="screenname" name="screenname" placeholder="screen name" /><br/>
                    <input type="checkbox" name="isadmin" value="true"> Administrator<br>
                </div>
                <div class="form-group">
                	<br>
                    <button type="submit" name="submit" id="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">Add Player</button>
                </div>
            </form>
        </div>

        <div class="box" style="padding:1em;padding-left:2%;padding-right:2%;margin:10px;">
            <h3 class="text-center">Edit Player</h3>
            <form class="form-horizontal" role="form" name ="edituser" method="post" action="edit-player.php">';
    $html .= $key;
    $html .= '
                <div class="form-group">
                    <select class="form-field" style="padding:10px" id="user" name="user">';
    foreach ($cafetch as $myUser) {
        $html .= '      <option value="' . $myUser['lms_player_id'] . '">' . $myUser['lms_player_screen_name'] . ' <small>(' . decrypt($myUser['lms_player_email']) . ')<small></option>';
    }
    $html .= '	    </select>
                </div>
				<div class="form-group">
                    <input id="submit" name="submit" type="submit" value="Select" class="btn graybutton" style="padding:5px;width:50%;">
				</div>
            </form>
        </div>

        <div class="box" style="padding:1em;padding-left:2%;padding-right:2%;margin:10px;">
            <h3 class="text-center">Change Password</h3>
            <form class="form-horizontal" role="form" name ="edituser" method="post" action="change-password.php" onsubmit="return validatePassReset()">';
    $html .= $key;
    $html .= '  <div class="form-group">

                    <select class="form-field" style="padding:10px" id="user" name="user">';
    foreach ($cafetch as $myUser) {
        $html .= '      <option value="' . $myUser['lms_player_id'] . '">' . $myUser['lms_player_screen_name'] . ' <small>(' . decrypt($myUser['lms_player_email']) . ')<small></option>';
    }
    $html .= '	    </select>
				</div>

                <div class="form-group">
                        <label for="pwd1">New password:</label>
                        <input name="pwd1" id="pwd1" class="form-field"  style="padding:10px" title="Password must contain at least 8 characters, including UPPERCASE, lowercase and numbers." type="password" onChange="checkPasswordMatch()">
                </div>
                <div class="form-group">
                        <label for="pwd1">Confirm password:</label>
                        <input id="pwd2" name="pwd2" class="form-field"  style="padding:10px" title="Please enter the same Password as above." type="password" onChange="checkPasswordMatch()">
                </div>
                <div class="form-group">
                        <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
                </div>
            </form>
        </div>
        <div class="box" style="text-align:left;width:950px;padding:1em;margin:10px;">
    		<h3 style="text-align:center">All Players</h3>
        	<table class="table table-bordered" id="keywords">
				<thead>
				    <tr class="info">
					   <th>Login</th>
					   <th>Forename</th>
					   <th>Surname</th>
					   <th>Screen Name</th>
					   <th>Access</th>
                       <th>Verified</th>
                       <th>Active</th>
				    </tr>
			    </thead>
			    <tbody>
									';
    foreach ($cafetch as $rs) {

        $active = ($rs['lms_active'] == 1 ? 'Yes' : 'No');
        $verified = ($rs['lms_player_email_verified'] == 1 ? 'Yes' : 'No');
        $rowcolor = ($rs['lms_active'] == 1 ? 'white' : 'silver');
        $rowcolor = ($rs['lms_access'] > 900 ? 'yellow' : $rowcolor);
        $html .= '
    				<tr style="color:' . $rowcolor . '">
    					<td>' . decrypt($rs['lms_player_login']) . '</td>
    					<td>' . decrypt($rs['lms_player_forename']) . '</td>
    					<td>' . decrypt($rs['lms_player_surname']) . '</td>
    					<td>' . $rs['lms_player_screen_name'] . '</td>
    					<td>' . $rs['lms_access'] . '</td>
                        <td>' . $verified . '</td>
                        <td>' . $active . '</td>
    				</tr>';
    }
    $html .= '
			     </tbody>
		      </table>
        </div>
        <div style="padding:2em;">
            <a href="' . $myPath . 'menus/home.php" class="btn" style="padding:15px;" role="button">Back</a>
        </div>
        ';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
