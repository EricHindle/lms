<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/week/week-functions.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access == 999) {
    $weekid = $_SESSION['currentseason'] . $_SESSION['currentweek'];
    $weekstate = get_week_state($weekid);
    $html = "";
    $key = $formKey->outputKey();
    echo '
				<!doctype html>
				<html>
					<head>

					    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
					    <meta charset="UTF-8">

					    <title>Week End Processing</title>

					    <meta name="viewport" content="width=device-width, initial-scale=1">
					    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
					    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
					    <script src="' . $myPath . 'js/jquery.js"></script>
					    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
					</head>
					        
					<body>';
    include $myPath . 'globNAV.php';
    $html .= '
						<section id="homeSection">
					    <br><br>
					        <div class="container">
            					<div class="row">
									<div class="col-md-8">
									    <h1><strong>Week End Processing for period ' . $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'] . '</strong></h1>
									</div>
								</div>
								<div class = "row">';

    $html .= '			   <div class="well col-md-6 col-md-offset-1 textDark">';

    if ($weekstate > 0) {
        $html .= ' <div class = "row">Confirmed : all matches have been resulted</div>';
        if ($weekstate > 1) {
            $html .= '   <div class = "row">Game player statuses marked up</div>';
            if ($weekstate > 2) {
                $html .= '    <div class = "row">Winners identified</div>';
                if ($weekstate > 3) {
                    $html .= '        <div class = "row">Game statuses marked up</div>';
                    if ($weekstate > 4) {
                        $html .= '           <div class = "row">Outcomes notified</div>';
                        if ($weekstate > 5) {
                            $html .= '            <div class = "row">Week rolled forward</div>';
                            $html .= ' 		<div class="row">
													<br>
													<div class="col-xs-6">
														<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">OK</a>
														<br>
													</div>
												</div> ';
                        } else {
                            /*
                             * Rolling week forward
                             */

                            set_week_state($weekid, 6);
                            header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
                        }
                    } else {
                        /*
                         * Notifying outcomes
                         */

                        set_week_state($weekid, 5);
                        header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
                    }
                } else {
                    /*
                     * Marking up game status
                     */

                    set_week_state($weekid, 4);
                    header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
                }
            } else {
                /*
                 * Checking for winners
                 */

                set_week_state($weekid, 3);
                header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
            }
        } else {
            /*
             * Marking up game player status
             */

            set_week_state($weekid, 2);
            header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
        }
    } else {
        /*
         * Confirming results
         */
        set_week_state($weekid, 1);
        header('Location: ' . $myPath . 'struct/week/week-end-processing.php');
    }

    $html .= '			   </div>
								</div>
                            </div>
                        </section>
            		</body>
				</html>';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}

?>