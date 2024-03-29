<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022 Eric Hindle. All rights reserved.
 */
$myPath = '../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$devlevelneeded = 901;
$formKey = new formKey();
$currentPage = '';
$key = $formKey->outputKey();

if (login_check($mypdo) == true && $_SESSION['retaccess'] == $devlevelneeded) {
    $html = '';
    echo '
		<!doctype html>
		<html>
            <head>
                <meta charset="UTF-8">
                <title>LML - Test Menu</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
        	</head>
			<body>';
    include $myPath . 'globNAV.php';
    $html .= '
			     <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Development and Testing</h2>
                    </div>                     
                    <div class="box" style="padding:1em;padding-left:10%;padding-right:10%;margin:10px;">
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'testing/testforms/viewapilog.php">
                                <h3 style="color:white;">API Log</h3>
                            </a>	
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'testing/testforms/viewplayerlog.php">
                                <h3 style="color:white;">Player Log</h3>
                            </a>	
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'testing/testforms/viewwendlog.php">
                                <h3 style="color:white;">Weekend Log</h3>
                            </a>	
                        </div>
                    </div>
                    <div class="box" style="padding:1em;padding-left:10%;padding-right:10%;margin:10px;">
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'testing/testforms/emailtest.php">
                                <h3 style="color:white;">Email test</h3>
                            </a>	
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'testing/testforms/encryptiontest.php">
                                <h3 style="color:white;" >Encryption test</h3>
                            </a>
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'struct/match/show-results.php">
                                <h3 style="color:white;" >Results</h3>
                            </a>
                        </div>
                        <div class="btn graybutton" style="padding:3px;margin:3px;width:100%;">
                            <a href="' . $myPath . 'testing/testforms/testrig.php">
                                <h3 style="color:white;" >Test a function</h3>
                            </a>
                        </div>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Read a JSON file</h3>
                        <form role="form" name ="json" method="post" action="' . $myPath . 'testing/testforms/jsontest.php">';
    $html .= $key;
    $html .= '              <div class="form-group " style="margin-left:16px;margin-right:16px">
                                <input type="text" class="form-control" id="filename" name="filename" placeholder="file name">
					        </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit2" name="submit" type="submit" value="Submit" class="btn graybutton" style="margin:10px;padding:5px;width:50%;">
					        </div>
					    </form>
			        </div>
		    	</div>
            </body>
        </html>';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
