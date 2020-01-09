<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath .'includes/formkey.class.php';
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
			    
			    <title>Focus Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
			    <script src="'.$myPath.'js/jquery.js"></script>
			    <script src="'.$myPath.'js/bootstrap.min.js"></script>
			</head>

			<body>';
				include $myPath.'globNAV.php';
				$focussql = "SELECT * FROM focus ORDER BY id ASC";
				$focusquery = $mypdo->prepare($focussql);
				$focusquery->execute();
				$focusfetch = $focusquery->fetchAll(PDO::FETCH_ASSOC);
		$html = '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Focus Admin</strong></h1>
			                </div>
			      		</div>
			      		 <div class="row">
			            	<div class="col-sm-4">
			                    <div class="tile red">
		                    		<h3 class="title" >1. '.$focusfetch[0]['title'].'</h3>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile blue">
		                    		<h3 class="title" >2. '.$focusfetch[1]['title'].'</h3>
			          			</div>
			                </div>
			                <div class="col-sm-4">
			                    <div class="tile green">
		                    		<h3 class="title" >3. '.$focusfetch[2]['title'].'</h3>
			          			</div>
			                </div>
			      		</div>
			      		<div class="row">
			      			<div class ="col-sm-4">
				      			<div class="well textDark">
					      			<form class="form-inline" role="form" name ="frm1" method="post" action="change-focus.php">
										<div class="form-group">
											<label for="focus1">Focus 1:</label>
											<input type="text" class="form-control" id="focustxt" name="focustxt">
											<input type="hidden" id ="foc" name="foc" value="1">
											';
		$html .= $key;
		$html .='
										</div>
										<button type="submit" class="btn btn-default">Change</button>
									</form>
								</div>
							</div>
							<div class ="col-sm-4">
				      			<div class="well textDark">
					      			<form class="form-inline" role="form" name ="frm2" method="post" action="change-focus.php">
										<div class="form-group">
											<label for="focus1">Focus 2:</label>
											<input type="text" class="form-control" id="focustxt" name="focustxt">
											<input type="hidden" id ="foc" name="foc" value="2">
											';
		$html .= $key;
		$html .='
										</div>
										<button type="submit" class="btn btn-default">Change</button>
									</form>
								</div>
							</div>
							<div class ="col-sm-4">
				      			<div class="well textDark">
					      			<form class="form-inline" role="form" name ="frm3" method="post" action="change-focus.php">
										<div class="form-group">
											<label for="focus1">Focus 3:</label>
											<input type="text" class="form-control" id="focustxt" name="focustxt">
											<input type="hidden" id ="foc" name="foc" value="3">
											';
		$html .= $key;
		$html .='
										</div>
										<button type="submit" class="btn btn-default">Change</button>
									</form>
								</div>
							</div>
			      		</div>
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="'.$myPath.'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>
