<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
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
				$place = $_SESSION['cluster'];
				$sql= "SELECT id, shop, datestart FROM svchks WHERE cluster = :place AND iscomplete = :iscomplete AND fordelete = :fordelete AND completedby = :me ORDER BY datecomplete DESC";
				break;
			case 'AM':
				$place = $_SESSION['area'];
				$sql= "SELECT id, shop, datestart FROM svchks WHERE area = :place AND iscomplete = :iscomplete AND fordelete = :fordelete AND completedby = :me ORDER BY datecomplete DESC";
				break;
			case 'RM':
				$place = $_SESSION['region'];
				$sql= "SELECT id, shop, datestart FROM svchks WHERE region = :place AND iscomplete = :iscomplete AND fordelete = :fordelete AND completedby = :me ORDER BY datecomplete DESC";
				break;
			case 'DD':
				$place = $_SESSION['division'];
				$sql= "SELECT id, shop, datestart FROM svchks WHERE division = :place AND iscomplete = :iscomplete AND fordelete = :fordelete AND completedby = :me ORDER BY datecomplete DESC";
				break;
			case 'Admin':
				$place = $_SESSION['division'];
				$sql= "SELECT id, shop, datestart FROM svchks WHERE division = :place AND iscomplete = :iscomplete AND fordelete = :fordelete AND completedby = :me ORDER BY datecomplete DESC";
				break;
			default:
				$place = $_SESSION['cluster'];
				$sql= "SELECT id, shop, datestart FROM svchks WHERE cluster = :place AND iscomplete = :iscomplete AND fordelete = :fordelete AND completedby = :me ORDER BY datecomplete DESC";
				break;
		}
		
		$query = $mypdo->prepare($sql);
		$query->execute(array(':place'=>$place, ':iscomplete'=>0, ':fordelete'=>0, ':me'=>$_SESSION['fname']));
		$count = $query->rowCount();
						
		echo '
			<!doctype html>
			<html>
				<head>
					
				    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
				    <meta charset="UTF-8">
				    
				    <title>Smart Visit</title>
				    
				    <meta name="viewport" content="width=device-width, initial-scale=1">
				    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
				    <link rel="stylesheet" href="'.$myPath.'css/retmanage.css">
				    <script src="'.$myPath.'js/jquery.js"></script>
				    <script src="'.$myPath.'js/bootstrap.min.js"></script>
				    <script src="'.$myPath.'js/jquery.tablesorter.js"></script>

				    <script>
			            $(function(){
			            $(\'#keywords\').tablesorter(); 
			            });
			        </script>
				</head>

				<body id="backWhite">';
					include $myPath.'globNAV.php';
					echo '	
					<div class="container-fluid">
						<br><br>
					';
		if($count>0){
			$fetch = $query->fetchAll(PDO::FETCH_ASSOC);
			$locerrors = array("User denied the request for Geolocation","Location information is unavailable","The request to get user location timed out","An unknown error occurred","Geolocation is not supported by this browser.");
			
			echo '
						<h1>Smart Visit<small class="pull-right">Incomplete</small></h1>
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-striped table-bordered" id="keywords" >
									<thead>
										<tr class="info">
											<th>ID</th>
											<th>LBO</th>
											<th>Started</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									';
									foreach ($fetch as $myResult)
									{
										$surveyend= DateTime::createFromFormat('Y-m-d H:i:s', $myResult['datestart']);
										$convenddate = $surveyend->format('d-m-Y H:i:s');
										echo '<td>'.$myResult['id'].'</td>';
										echo '<td>'.$myResult['shop'].'</td>';
										echo '<td>'.$convenddate.'</td>';
										echo '<td class="text-center">
												<div class="col-xs-12">
													<div class = "row">
														<a href="'.$myPath.'trackers/sv/sv-proc-continue.php?r='.$myResult['id'].'" class="btn btn-success btn-small" role="button">Resume</a>
													</div>
													<div class="help-block"></div>
													<div class = "row">
													<a href="'.$myPath.'trackers/sv/sv-proc-del.php?r='.$myResult['id'].'" onclick="return confirm(\'Are you sure you delete this survey?\')" class="btn btn-warning btn-danger" role="button">Remove</a>
													</div>
												</div>
											</td>';
										echo "</tr>\n";
										
									}
								echo '
									</tbody>
							</table>
						</div>
					</div>
				';
		} else  {
			echo '<h1>No records found</h1>';
		}
		echo '
		
				<br><br>
				<div class="row">
					<div class="col-xs-6">
						<a href="'.$myPath.'trackers/sv/sv-main.php" class="btn btn-primary btn-lg" role="button">Back</a>
					</div>
					<br><br><br><br>
				</div> 
			</body>
		</html>';
	} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
