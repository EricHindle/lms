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
$formKey = new formKey();
$currentPage = 'join';
$key = $formKey->outputKey();
$html = '';
if (login_check($mypdo) == true) {
    echo '
    <!doctype html>
        <html>
        	<head> 
                <meta charset="UTF-8" />
                <title>Join Game - Last Man Live</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        		<link rel="stylesheet" href="' . $myPath . 'css/style.css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
            </head>
        	<body>          
            ';
    include $myPath . 'globNAV.php';
    echo '
                <div class="page-container">
                    <div class="page-container">
                    <div class="box">
                        <h2>Join a Game</h2>
                        <p>Enter a code to join an already existing game.</p><br>
                        <form class="form-horizontal" role="form" name ="edit" method="post" action="' . $myPath . 'struct/game/process-join-game.php">';
    $html .= $key;
    $html .= '
                            <input type="text" class="form-field" id="gamecode" name="gamecode" placeholder="Enter Game Code">
                            <button type="submit" name="submit" id="submit" value="Submit" class="btn">Join Game</button>
                        </form>
                    </div>
                </div>

            </body>
        </html>
    	';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?> 
