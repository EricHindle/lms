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
$formKey = new formKey();
$key = $formKey->outputKey();

$html = "";

echo '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="' . $myPath . 'css/login.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
	<title>Last Man Live - Reset Password</title>
</head>
<body>
<div class="container">
	<div class="box">	
		<h1 style="margin-bottom:10px">Create account</h1>
		<form class="form-group" role="form" name ="addplayer" autocomplete="off" method="post" action="' . $myPath . 'struct/player/add-new-player.php">';
$html .= $key;
$html .= '					
		<input type="text" class="form-field" name="email"  id="email" placeholder="email address"  value="" >
		<input type="password" class="form-field" name="password"  id="password" placeholder="password" autocomplete="off"  value="" >
        <input type="password" class="form-field" name="confirm"  id="confirm" placeholder="confirm password" autocomplete="off"  value="" >
        <input type="text" class="form-field" name="fname" id="fname" placeholder="first name" />
        <input type="text" class="form-field" name="sname" id="sname" placeholder="surname" />
        <input type="text" class="form-field" name="screenname" id="screenname"  placeholder="screen name" />
		<input type="hidden" name="isadmin" value="false">
		<div class="form-checkbox">
		<input type="checkbox" style="margin-left:20px;" name="issendemail" id="issendemail" value="true" checked >
		<label for="issendemail">&nbsp receive game emails</label>
		</div>
        <input id="submit" name="submit" type="submit" value="Submit" class="btn">
		</form>
		</div>
		<div class="light-text">
        <a href="' . $myPath . 'index.php" role="button">Back</a>
			            ';

$html .= '	      		
			    	</div>
					</div>
			</body>
		</html>

		';
echo $html;

?>
