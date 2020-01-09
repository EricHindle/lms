<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
	sec_session_start();

	if(login_check($mypdo) == true) { 
        if (isset($_GET['s']))
        {
        	$id=sanitize_int($_GET['s']);
            if($id)
            {
				$sql= "SELECT * FROM svlitechkscomp WHERE id = :id LIMIT 1";
				$query = $mypdo->prepare($sql);
				$query->execute(array(':id'=>$id));
				$count = $query->rowCount();
				$html="";
				if($count>0){
					$fetch = $query->fetch(PDO::FETCH_ASSOC);
					$str = file_get_contents($myPath.'trackers/svlite/questions/svlite.json');
					$json = json_decode($str, true); // decode the JSON into an associative array
					$sectionCounter = 0;
					$locerrors = array("User denied the request for Geolocation","Location information is unavailable","The request to get user location timed out","An unknown error occurred","Geolocation is not supported by this browser.");
					$surveystart= DateTime::createFromFormat('Y-m-d H:i:s', $fetch['datestart']);
					$convstartdate = $surveystart->format('d-m-Y H:i:s');
					$surveyend= DateTime::createFromFormat('Y-m-d H:i:s', $fetch['datecomplete']);
					$convenddate = $surveyend->format('d-m-Y H:i:s');
					$html.= '
					<!doctype html>
					<html>
						<head>
							
						    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
						    <meta charset="UTF-8">
						    
						    <title>Record detail</title>
						    
						    <meta name="viewport" content="width=device-width, initial-scale=1">
						    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
						    <link rel="stylesheet" href="'.$myPath.'css/retmanage.css">
						    <script src="'.$myPath.'js/jquery.js"></script>
						    <script src="'.$myPath.'js/bootstrap.min.js"></script>
						    <script src="https://maps.google.com/maps/api/js?key=AIzaSyDTBvkVKG6tymSlAZjGLvZwjMIN4Hq4ac4"></script>
						    <script src="'.$myPath.'js/showloc.js"></script>
						</head>

						<body id="backWhite">';
							include $myPath.'globNAV.php';
					$html.=  '	
							<div class="container">
								<br><br><br>
								<h1 class="text-center">'.$fetch['shop'].'</h1>
								<p class="text-center">Score: '.$fetch['score'].'%</p>
								<br>
								<div class="panel-group">
								';
					foreach ($json['questions']as $value) {	
						if ($value['section']>$sectionCounter) {
							if ($value['section']>1) {
								$commentid=$value['section']-1;
								$html.='
										
											<h4>Comments:</h4>
											<p>'.$fetch['comment'.$commentid].'</p>
										</div>	
									</div>
									<br>	
								';
							}
							$html.= '
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h4>'.$json['sectionNames'][$sectionCounter].'</h4>
										</div>
										<div class="panel-body">
										';
							$sectionCounter++;
						}
							$indexname = 'q'.$value['id'];
							if (is_numeric($fetch[$indexname])) {
								$my_answer=$fetch[$indexname].'/5';
							} else {
								$my_answer=$fetch[$indexname]; 
							}
							$html.= '
											<h4>'.$value['id'].') '. $value['question'].'</h4>
											<p>Answer: '.$my_answer.'</p>
											<br>
							';
					}

					$html.='
											
											<h4>Comments</h4>
											<p>'.$fetch['comment'.$json['noSections']].'</p>
										</div>
									</div>
									<br>

					';

					$latCheck = in_array($fetch['lat'], $locerrors, true);
					$lonCheck = in_array($fetch['lon'], $locerrors, true);
					$lochtml = "";
					if ($latCheck) {
						$lochtml = $fetch['lat'];
					} elseif ($lonCheck) {
						$lochtml = $fetch['lon'];
					} else {
						$lochtml = 'Location: '.$fetch['lat'].', '.$fetch['lon'];
						$html.='
									<div class="panel panel-primary">
										<div class="panel-heading">
										<h4>Location</h4>
										</div>
										<div class="panel-body" id="mapholder">
											
										</div>
										<div class="panel-footer">
											<p>'.$lochtml.'<p>
										</div>
									</div>
									<br>
									<script>showPosition('.$fetch['lat'].','.$fetch['lon'].')</script>
						';
					}
					$html.= '

									<div class="panel panel-primary">
										<div class="panel-heading">
										<h4>Summary</h4>
										</div>
										<div class="panel-body">
											<p>Record ID: SVL-'.$fetch['id'].'<p>
											<p>Started: '.$convstartdate.'<p>
											<p>Finished: '.$convenddate.'<p>
											<p>Score: '.$fetch['score'].'%</p>
											<p>Non-applicable questions: '.$fetch['na'].'</p>
											<p>CEM: '.$fetch['cem'].'</p>
											<p>Completed by: '.$fetch['completedby'].'</p>
										</div>
									</div>
								</div>
								<br><br>
								<div class="row">
									<div class="col-xs-6">
										<a href="svlite-records.php" class="btn btn-primary btn-lg" role="button">Back</a>
									</div>
									<br><br><br><br>
									
								</div>
							</div> 
						</body>
					</html>';
					echo $html;

				} else  {
					bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not found in DB");
				}
            } else {
            	bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Suspicious data");
        	}
        } else {
            bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "GET data missing");
        }
	} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
