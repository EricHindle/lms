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
				//Mod for bpms in region 1 to see their full area
				if ($_SESSION['region']=='1') {
					$place = $_SESSION['area'];
					$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE area = :place ORDER BY datecomplete DESC";
				} else {
					$place = $_SESSION['cluster'];
					$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE cluster = :place ORDER BY datecomplete DESC";
				}
				break;
			case 'AM':
				$place = $_SESSION['area'];
				$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE area = :place ORDER BY datecomplete DESC";
				break;
			case 'RM':
				$place = $_SESSION['region'];
				$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE region = :place ORDER BY datecomplete DESC";
				break;
			case 'DD':
				$place = $_SESSION['division'];
				$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE division = :place ORDER BY datecomplete DESC";
				break;
			case 'Admin':
				$place = $_SESSION['division'];
				$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE division = :place ORDER BY datecomplete DESC";
				break;
			default:
				$place = $_SESSION['cluster'];
				$sql= "SELECT id, shop, score, datecomplete, veperiod FROM svlitechkscomp WHERE cluster = :place ORDER BY datecomplete DESC";
				break;
		}
		$query = $mypdo->prepare($sql);
		$query->execute(array(':place'=>$place));
		$count = $query->rowCount();
						
		echo '
			<!doctype html>
			<html>
				<head>
					
				    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
				    <meta charset="UTF-8">
				    
				    <title>Reports</title>
				    
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
						<h1>Smart Visit Lite<small class="pull-right">Reports</small></h1>
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-striped table-bordered" id="keywords" >
									<thead>
										<tr class="info">
											<th>ID</th>
											<th>LBO</th>
											<th>Score (%)</th>
											<th>Date</th>
											<th>Period</th>
										</tr>
									</thead>
									<tbody>
									';
									foreach ($fetch as $myResult)
									{
										$surveyend= DateTime::createFromFormat('Y-m-d H:i:s', $myResult['datecomplete']);
										$convenddate = $surveyend->format('d-m-Y H:i:s');
										echo '<td><a href="svlite-detail.php?s='.$myResult['id'].'">SVL-'.$myResult['id'].'</a></td>';
										echo '<td>'.$myResult['shop'].'</td>';
										echo '<td>'.$myResult['score'].'</td>';
										echo '<td>'.$convenddate.'</td>';
										echo '<td>'.$myResult['veperiod'].'</td>';
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
						<a href="'.$myPath.'menus/reports.php" class="btn btn-primary btn-lg" role="button">Back</a>
					</div>
					<br><br><br><br>
				</div> 
			</body>
		</html>';
	} else { 
	        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>
