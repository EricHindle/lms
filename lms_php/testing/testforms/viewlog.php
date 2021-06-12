<?php
$myPath = '../../';
require_once $myPath . 'includes/db_connect.php';
require_once $myPath . 'includes/functions.php';
require_once $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();
$_SESSION['currentweek'] = get_global_value('currweek');
$_SESSION['currentseason'] = get_global_value('currseason');
$_SESSION['selectweek'] = get_global_value('selectweek');
$_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
        $logfilename = "../..//logs/lml-log-" . $_SESSION['matchweek'] . ".log";
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
                <link rel="stylesheet" href="../../../css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
        	</head>
                    
        	<body  >';
            $html .= '
			        <div class="container">
			        	<div class="box" style="width:350px;padding: 1.5em;height: 140px;">
			                    <h1 style="text-align:center;"><strong>View Logfile</strong></h1>
<h3 style="text-align:center; "><strong>';
                $html .= $logfilename;
                $html .= '</strong></h3>
			                    <br> </div>
<div class="box" style="width:90%;padding: 1em;text-align:left"> '. $contents . ' </div>
			                </div>
			            ';

            $html .= '
			    	</div>
			</body>
		</html>
                    
		';

            echo $html;

    




?>   