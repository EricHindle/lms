<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();

    $remainingweeks = get_remaining_weeks(true);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    <title>Game Weeks</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
			    <script>
		            $(function(){
		            $(\'#keywords\').tablesorter(); 
		            });
		        </script>
			</head>

			<body>';
    include $myPath . 'globNAV.php';
    $html .= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-11">
			                    <h1><strong>Week End Admin for period ' . $_SESSION['currentweek'] . '/' . $_SESSION['currentseason'] . '</strong></h1>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			      		</div>
			        	<div class = "row">';

    $html .= '		    <div class="col-sm-4 col-sm-offset-1">
                            <div class="tile red">
                            	<h3 class="title" >Enter Results</h3>
			                	    <form class="form" role="form" name ="results" method="post" action="enter-results.php">';
    $html .= $key;
    $html .= '				
				                        <h5 class="text-center">Enter or amend match results</h5>
                                        <br>
				                        <div>
				                            <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 col-sm-offset-1">
                                <div class="tile orange">
                                    <h3 class="title" >Complete the week</h3>   ';
    $html .= $key;
    $html .= '                      <ul>
                                        <li>Confirm that all results have been entered</li>
                                        <li>Update player status in all active games</li>
                                        <li>Check for game winners</li>
                                        <li>Update game status in all active games</li>
                                        <li>Notify outcomes</li>
                                        <li>Move week forward</li>
                                    </ul>
                                    <br>				  	    
    		                        <div>
    		                          <a href="' . $myPath . 'struct/week/week-end-processing.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Submit</a>
    		                        </div>
                                </div>';
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 1) {
            $html .= ' <div class="alert alert-danger">Not all matches have been resulted. Enter results and try again.</div> ';
        }
    }

    $html .= '                 </div>
                           </div>
                           <div class="row">
						      <br>
						      <div class="col-xs-6">
						          <a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
						          <br>
				              </div>
						  </div>
			          </div>
			    </section>
			</body>
		</html>

		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
