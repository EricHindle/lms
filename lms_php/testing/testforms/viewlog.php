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
$currentPage = '';
$devlevelneeded = 901;
$formKey = new formKey();
$key = $formKey->outputKey();

if (login_check($mypdo) == true && $_SESSION['retaccess'] == $devlevelneeded) {
    
    $_SESSION['currentweek'] = get_global_value('currweek');
    $_SESSION['currentseason'] = get_global_value('currseason');
    $_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
    $logfilename = "../../logs/lml-log-" . $_SESSION['matchweek'] . ".log";
    $period = $_SESSION['matchweek'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['form_key'])) {
            if (isset($_POST['period'])) {
                $period = $_POST['period'];
                $logfilename = $myPath .  "logs/lml-log-" . $period . ".log";
            }
        }
    }
    
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
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
        	</head>
                    
        	<body>';
    include $myPath . 'globNAV.php';
    $html .= '
			     <div class="container">
			         <div class="box" style="width:350px;padding: 0.5em;height: 160px;">

                       <form role="form" name ="json" method="post" action="' . $myPath . 'testing/testforms/viewlog.php">';
    $html .= $key;
    $html .= '              
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit2" name="submit" type="submit" value="View Logfile" class="btn graybutton" style="margin:10px;padding:5px;">
					        </div>
                            <div class="form-group " style="margin-left:16px;margin-right:16px">
                                Period <input type="text" class="form-control" id="period" name="period" placeholder="period" value="' . $period .'">
					        </div>

					    </form>
                        <h5>' . $logfilename . ' </h5>
			         </div>
                     <div class="box" style="width:90%;padding: 1em;text-align:left;margin:10px"> ' . $contents . ' </div>';
    $html .= '           
			    </div>
			</body>
		</html>';

    echo $html;

} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>   