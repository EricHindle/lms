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

echo '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="' . $myPath . 'css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
	<title>Last Man Live - Reset Password</title>
</head>
<body>			
	<div class="container">
		<div class="box">	
			<h1 style="margin-bottom:10px">Request a new password</h1>
			<form class="form-group" role="form" name ="addplayer" autocomplete="off" method="post" action="' . $myPath . 'struct/player/add-temp-password.php">
';

$html .= $key;

$html .= '			
				<input type="text" class="form-field" name="email" id="email" placeholder="email address"  value="" >
        		<input id="submit" name="submit" type="submit" value="Submit" class="btn">
			</form>
		</div>	      		
		<div class="light-text">
			<a href="' . $myPath . 'index.php" role="button">Back</a>
		</div>
	</div>
</body>
</html>

';  

echo $html; ?>
