<?php
/*
 * HINDLEWARE
 * Copyright (C) 2021 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require_once $myPath . 'includes/db_connect.php';
require_once $myPath . 'includes/functions.php';
require_once $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();
$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
$logfilename = "../../logs/lml-log-" . $_SESSION['matchweek'] . ".log";
$logfile = fopen($logfilename, "r");
$contents = '';
if ($logfile) {
    while (($line = fgets($logfile)) !== false) {
        $contents .= $line . '<br/>';
    }

    fclose($logfile);
}
$html = ' <!doctype html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>View Logfile</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <link rel="stylesheet" href="' . $myPath . '../css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
        	</head>
                    
        	<body>';
$html .= '
			     <div class="container">
			         <div class="box" style="width:350px;padding: 0.5em;height: 80px;">
			             <h3 style="text-align:center;">View Logfile</h3>
                         <h5 style="text-align:center; ">';
$html .= $logfilename;
$html .= '               </h5>
			         </div>
                     <div class="box" style="width:90%;padding: 1em;text-align:left;margin:10px"> ' . $contents . ' </div>';
$html .= '           <div style="padding:2em;">
						  <a href="' . $myPath . 'menus/testmenu.php" class="btn" style="padding:15px;" role="button">Back</a>
				     </div>
			    </div>
			</body>
		</html>';

echo $html;

?>   