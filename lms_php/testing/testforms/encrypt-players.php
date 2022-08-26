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

    $playerchangesql = "SELECT * FROM lms_player ORDER BY lms_player_screen_name ASC";
    $playerchangequery = $mypdo->prepare($playerchangesql);
    $playerchangequery->execute();
    $cafetch = $playerchangequery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta charset="UTF-8">
			    <title>Player Encryption</title>
			    
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
			</head>

			<body>';
    include $myPath . 'globNAV.php';
    $html .= '
<div class="container">
        <div class="box"  style="padding:1em;">
            <h2>Player Encryption</h2>
        </div>';
    if (! $_SESSION['encrypted']) {
        $html .= '        <div class="box" style="text-align:left;width:850px;padding:1em;margin:10px;">
    		<h3 style="text-align:center">All Players</h3>
        	<table class="table table-bordered" id="keywords">
				<thead>
				    <tr class="info">
					   <th>Login</th>
					   <th>Forename</th>
					   <th>Surname</th>
					   <th>Screen Name</th>
				    </tr>
			    </thead>
			    <tbody>
									';
        foreach ($cafetch as $rs) {
            $userid = $rs['lms_player_id'];
            $email = $rs['lms_player_login'];
            $fname = $rs['lms_player_forename'];
            $sname = $rs['lms_player_surname'];
            $mobile = $rs['lms_player_mobile'];

            $email_e = encrypt($email);
            $fname_e = encrypt($fname);
            $sname_e = encrypt($sname);
            $mobile_e = encrypt($mobile);

            $upsql = "UPDATE lms_player SET lms_player_email = :email, lms_player_login = :username, lms_player_forename = :forename,  lms_player_surname = :surname, lms_player_mobile = :mobile WHERE lms_player_id = :id";
            $upduser = $mypdo->prepare($upsql);
            $upduser->bindParam(':username', $email_e);
            $upduser->bindParam(':forename', $fname_e);
            $upduser->bindParam(':surname', $sname_e);
            $upduser->bindParam(':email', $email_e);
            $upduser->bindParam(':mobile', $mobile_e);
            $upduser->bindParam(':id', $userid, PDO::PARAM_INT);
            $upduser->execute();

            // $email_d = decrypt($email);
            // $fname_d = decrypt($fname);
            // $sname_d = decrypt($sname);

            $active = ($rs['lms_active'] == 1 ? 'Yes' : 'No');
            $rowcolor = ($rs['lms_active'] == 1 ? 'white' : 'silver');
            $rowcolor = ($rs['lms_access'] > 900 ? 'yellow' : $rowcolor);
            $html .= '
    				<tr style="color:' . $rowcolor . '">
    					<td>' . $rs['lms_player_screen_name'] . '</td>
    					<td>' . $email . '</td>
    					<td>' . $fname . '</td>
    					<td>' . $sname . '</td>

    				</tr>
    				<tr style="color:' . $rowcolor . '">
    					<td>' . $rs['lms_player_screen_name'] . '</td>
    					<td>' . $email_e . '</td>
    					<td>' . $fname_e . '</td>
    					<td>' . $sname_e . '</td>
    				</tr>';
            // <tr style="color:' . $rowcolor . '">
            // <td>' . $rs['lms_player_screen_name'] . '</td>
            // <td>' . $email_d . '</td>
            // <td>' . $fname_d . '</td>
            // <td>' . $sname_d . '</td>
            // </tr>
            // ';
        }
        set_global_value('encrypt', 'true', false);
        $_SESSION['encrypt'] = true;
        $html .= '
			     </tbody>
		      </table>
        </div>';
    }
    $html .= '       <div style="padding:2em;">
            <a href="' . $myPath . 'menus/home.php" class="btn" style="padding:15px;" role="button">Back</a>
        </div>
    </div>
</body>
        ';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
