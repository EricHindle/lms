<?php
$myPath = '../';
require_once $myPath . 'includes/db_connect.php';
require_once $myPath . 'includes/functions.php';
require_once $myPath . 'includes/formkey.class.php';

sec_session_start();

$formKey = new formKey();
$key = $formKey->outputKey();

if (login_check($mypdo) == true && $_SESSION['retaccess'] == 901) {
    $html = '';
    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>Development</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			</head>

			<body>';
    include $myPath . 'globNAV.php';
    echo '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Development and Testing</strong></h1>
			                    <br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
			                    	<a href="' . $myPath . 'testing/emailtest.php">
			                    		<h3 class="title" >Email test</h3>
			                        </a>	
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
                                    <h3 class="title" >Read a JSON file</h3>
				                	<form role="form" name ="json" method="post" action="' . $myPath . 'testing/jsontest.php">';
    $html .= $key;
    $html .= '                          <div class="form-group " style="margin-left:16px;margin-right:16px">
					                        <input type="text" class="form-control" id="filename" name="filename" placeholder="file name">
					                    </div>
					                    <div class="form-group" style="margin-left:16px;margin-right:16px">
					                    	<br>
					                        <input id="submit2" name="submit" type="submit" value="Submit" class="btn btn-primary">
					                    </div>
					                </form>
			          			</div>
			                </div>
			      		</div>

			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
							</div>
						</div>
			      		<br><br><br><br>
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
