<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';
	sec_session_start(); 
	if (isset($_SESSION['svid'])) {
		unset($_SESSION['svid']);
	}
	if (isset($_SESSION['svsec'])) {
		unset($_SESSION['svsec']);
	}
	if(login_check($mypdo) == true) {
		$role = $_SESSION['role'];
		switch ($role) {
			case 'BPM':
				//Mod for bpms in region 1 to see their full area
				if ($_SESSION['region']=='1') {
					$place = $_SESSION['area'];
					$vesql = "SELECT id, shop FROM lbos WHERE area= :place AND active = 1 ORDER BY shop ASC";
				} else {
					$place = $_SESSION['cluster'];
					$vesql = "SELECT id, shop FROM lbos WHERE cluster= :place AND active = 1 ORDER BY shop ASC";
				}
				break;
			case 'AM':
				$place = $_SESSION['area'];
				$vesql = "SELECT id, shop FROM lbos WHERE area= :place AND active = 1 ORDER BY shop ASC";
				break;
			case 'RM':
				$place = $_SESSION['region'];
				$vesql = "SELECT id, shop FROM lbos WHERE region= :place AND active = 1 ORDER BY shop ASC";
				break;
			case 'DD':
				$place = $_SESSION['division'];
				$vesql = "SELECT id, shop FROM lbos WHERE division= :place AND active = 1 ORDER BY shop ASC";
				break;
			case 'Admin':
				$place = $_SESSION['division'];
				$vesql = "SELECT id, shop FROM lbos WHERE division= :place AND active = 1 ORDER BY shop ASC";
				break;
			default:
				$place = $_SESSION['cluster'];
				$vesql = "SELECT id, shop FROM lbos WHERE cluster= :place AND active = 1 ORDER BY shop ASC";
				break;
		}

		$vequery = $mypdo->prepare($vesql);
		$vequery->execute(array(':place'=>$place));
		$vefetch = $vequery->fetchAll(PDO::FETCH_ASSOC);
		$formKey = new formKey(); 
		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Smart Visit Lite</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
			    <script src="'.$myPath.'js/jquery.js"></script>
			    <script src="'.$myPath.'js/bootstrap.min.js"></script>
			   
				<script>
					function validateForm() {
						$("#warning1").hide();

						var canSubmit = true;
			    		var cem_num = document.forms["veform"]["cem_num"].value;
			    		if (cem_num == null || cem_num == "" || cem_num.length!=7) {
							$("#warning1").show();
			        		canSubmit = false;
			    		} else {
			    			canSubmit = confirm("Is the CEM number "+cem_num+" correct?");
			    		}
						return canSubmit;
					}

					function maxLengthCheck(object) {
						if (object.value.length > object.max.length)
						object.value = object.value.slice(0, object.max.length)
					}

					function isNumeric (evt) {
						var theEvent = evt || window.event;
						var key = theEvent.keyCode || theEvent.which;
						key = String.fromCharCode (key);
						var regex = /[0-9]|\./;
						if ( !regex.test(key) ) {
							theEvent.returnValue = false;
							if(theEvent.preventDefault) theEvent.preventDefault();
						}
					}
				</script>
				
			</head>

			<body>';
				include $myPath.'globNAV.php';
		$html = '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			            <div class="row">
			                <div class="col-md-12">
			                    <h3 class="addShad"><strong>Smart Visit Lite</strong></h3>
			                    <br><br>
			                </div>
			            </div>
			            <div class="row">
			            	<div class="col-sm-12">
			            		<div class="well" id="veform">
				                    <form role="form" name ="veform" method="post" action="svlite-proc-create.php" onsubmit="return validateForm()">
				                    ';
		$html.=	$formKey->outputKey();
		$html.= '
				                    	<div class="form-group">
				                    		<h4 for="cem_num">Please enter CEM employee number:</h4>
				                    		<input type="number"
				                    		class="form-control input-lg"
				                    		id="cem_num" 
				                    		name="cem_num"  
				                    		onkeypress="return isNumeric(event)" 
				                    		max="9999999" 
				                    		min = "0000000"
				                    		oninput="maxLengthCheck(this)";/>
				                    		<div id="warning1" class="alert alert-danger collapse">
												Must be a 7 digit number.
											</div>
				                    	</div>
				                    	<div class="form-group">
				                    		<h4 for="lbo">Please select shop name:</h4>
				                    		<select class="form-control input-lg" id="lbo" name="lbo">
				                    		';
				                    		foreach ($vefetch as $shop) {
				                    			$html.=  '<option value="'.$shop['id'].'">'.$shop['shop'].'</option>';
				                    		}
		$html.= '
											</select>
											<br>
				                    	</div>
				                    	<div class="form-group">
				                    		<input id="submit" name="submit" type="submit" value="Start" class="btn btn-block btn-success btn-lg">
				                    	</div>
				                    </form>
				                </div>
			                </div>
			      		</div>
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="'.$myPath.'trackers/svlite/svlite-main.php" class="btn btn-primary btn-lg" role="button">Back</a>
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
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
