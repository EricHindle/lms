<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	require $myPath . 'includes/formkey.class.php';
	sec_session_start(); 
	if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
		$formKey = new formKey();
		$key = $formKey->outputKey();

		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Select dates</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap-datepicker3.standalone.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/retmanage.css">
			    <script src="'.$myPath.'js/jquery.js"></script>
			    <script src="'.$myPath.'js/bootstrap.min.js"></script>
			    <script src="' . $myPath . 'js/bootstrap-datepicker.min.js"></script>
			    <script>
			    	$(document).ready(function(){
						$(\'#sandbox-container .input-daterange\').datepicker({
							format: "dd/mm/yyyy",
						});
					});
			    </script>
			</head>

			<body>';
				include $myPath.'globNAV.php';
		echo '
				<br><br><br><br><br><br><br>
				<section id="homeSection">
			    <br><br>
			        <div class="container backwhite">
			      		<div class="row">
			      			<div class="well well-xs col-xs-10 col-xs-offset-1">
								<h2 class="text-center">Select survey dates</h2>
								<form class="form-horizontal" role="form" name ="showcalls" method="post"  action="show_report.php"">
									' . $key . '
									<div class="form-group row" name="fg2">
										<div class="col-xs-6 col-xs-offset-3">
											<label for="start">Select Dates</label>
											<div id="sandbox-container">
												<div class="input-daterange input-group " id="datepicker">
												    <input type="text" class="input-sm form-control" name="start" />
												    <span class="input-group-addon">to</span>
												    <input type="text" class="input-sm form-control" name="end" />
												</div>
											</div>
											<br>
											<input id="submit" name="submit" type="submit" value="Submit" class="btn btn-block btn-success btn-lg">
										</div>
									</div>
								</form>
							</div>
			      		</div>
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
							</div>
						</div>
			      		<br><br><br><br>
		    	</div>
		    </section>
		</body>
	</html>

		';
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>
