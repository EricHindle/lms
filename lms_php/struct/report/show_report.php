<?php
$myPath='../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
sec_session_start(); 
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
	if (isset($_POST['start'])) {
		$startDate = $_POST['start'];
	} else {
		$startDate = '13/01/2016';
	}
	if (isset($_POST['end'])) {
		$endDate = $_POST['end'];
	} else {
		$endDate = '13/01/2016';
	}
	if ($startDate == '') {
		$startDate = '13/01/2016';
	}
	if ($endDate == '') {
		$endDate = '13/01/2016';
	}
	$queryEndDate = date_format(date_create_from_format('d/m/Y', $endDate), 'Y-m-d');
	$queryStartDate = date_format(date_create_from_format('d/m/Y', $startDate), 'Y-m-d');
	$queryEndDate = $queryEndDate . ' 23:59:59';
	$queryStartDate = $queryStartDate . ' 00:00:00';

	$get_cluster_sql = "SELECT division, region, area, cluster FROM bpm_comp_rates ORDER BY division DESC, region ASC, area ASC, cluster ASC";
	$get_cluster_query = $mypdo->prepare($get_cluster_sql);
	$get_cluster_query->execute();
	$cluster_fetch = $get_cluster_query->fetchAll(PDO::FETCH_ASSOC);

	$cluster_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svchkscomp WHERE cluster = :cluster AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$cluster_complete_query = $mypdo->prepare($cluster_complete_sql);
	$area_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svchkscomp WHERE area = :area AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$area_complete_query = $mypdo->prepare($area_complete_sql);
	$region_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svchkscomp WHERE region = :region AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$region_complete_query = $mypdo->prepare($region_complete_sql);
	$division_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svchkscomp WHERE division = :division AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$division_complete_query = $mypdo->prepare($division_complete_sql);
	$national_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svchkscomp WHERE cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$national_complete_query = $mypdo->prepare($national_complete_sql);

	$svl_cluster_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svlitechkscomp WHERE cluster = :cluster AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$svl_cluster_complete_query = $mypdo->prepare($svl_cluster_complete_sql);
	$svl_area_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svlitechkscomp WHERE area = :area AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$svl_area_complete_query = $mypdo->prepare($svl_area_complete_sql);
	$svl_region_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svlitechkscomp WHERE region = :region AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$svl_region_complete_query = $mypdo->prepare($svl_region_complete_sql);
	$svl_division_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svlitechkscomp WHERE division = :division AND cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$svl_division_complete_query = $mypdo->prepare($svl_division_complete_sql);
	$svl_national_complete_sql = "SELECT count(DISTINCT cem) as mytotal FROM svlitechkscomp WHERE cem <> '0000000' AND datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$svl_national_complete_query = $mypdo->prepare($svl_national_complete_sql);

	$cluster_bpm_sql = "SELECT total FROM bpm_comp_rates WHERE cluster = :cluster";
	$cluster_bpm_query = $mypdo->prepare($cluster_bpm_sql);
	$area_bpm_sql = "SELECT SUM(total) as bpms FROM bpm_comp_rates WHERE area = :area";
	$area_bpm_query = $mypdo->prepare($area_bpm_sql);
	$region_bpm_sql = "SELECT SUM(total) as bpms FROM bpm_comp_rates WHERE region = :region";
	$region_bpm_query = $mypdo->prepare($region_bpm_sql);
	$division_bpm_sql = "SELECT SUM(total) as bpms FROM bpm_comp_rates WHERE division = :division";
	$division_bpm_query = $mypdo->prepare($division_bpm_sql);
	$national_bpm_sql = "SELECT SUM(total) as bpms FROM bpm_comp_rates";
	$national_bpm_query = $mypdo->prepare($national_bpm_sql);
	// ORDER BY division DESC, region, ASC, area ASC, cluster ASC
	//die(var_dump($cluster_fetch));
	///////////////////////////////////////////
	echo '
			<!doctype html>
			<html>
				<head>

				    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
				    <meta charset="UTF-8">

				    <title>Completion Report</title>

				    <meta name="viewport" content="width=device-width, initial-scale=1">
				    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
				    <link rel="stylesheet" href="' . $myPath . 'css/retmanage.css">
				    <script src="' . $myPath . 'js/jquery.js"></script>
				    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
				    <script src="' . $myPath . 'js/tabcollapse.js"></script>
				    
				</head>

				<body id="backWhite">';
		include $myPath . 'globNAV.php';
		echo '	<div class="container">
					<br><br><br><br>
					<br>
					<div class="row text-center">
						<h1>Completion report</h1>
						<p>' . $startDate . '  to  ' . $endDate . '</p>
						<br>
					</div>
					<div class="row">
						<table class="table table-striped table-bordered" id="tree-table" >
							<thead>
							<tr>
								<th  class="info"></th>
								<th  class="info">Colleagues</th>
								<th  class="info">SV Complete</th>
								<th  class="info">SV Percentage</th>
								<th  class="info">SVL Complete</th>
								<th  class="info">SVL Percentage</th>
							</tr>
							</thead>
							<tbody>
		';

		$prev_division = "";
		$prev_region = "";
		$prev_area = "";
		$id_count = 0;
		$division_id = 0;
		$region_id = 0;
		$area_id = 0;

		$colleagues_array = array();
		$percentage_array = array();



		foreach ($cluster_fetch as $cluster) {
			$id_count++;
			if ($cluster['division']!=$prev_division) {
				$division_complete_query->execute(array(':division'=>$cluster['division'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
				$division_complete_fetch = $division_complete_query->fetch(PDO::FETCH_ASSOC);
				$division_bpm_query->execute(array(':division'=>$cluster['division']));
				$division_bpm_fetch = $division_bpm_query->fetch(PDO::FETCH_ASSOC);
				$comp_percent = 0.00;
				if ($division_complete_fetch['mytotal']==0) {
					$comp_percent = 0;
				} else {
					if ($division_complete_fetch['mytotal']>=$division_bpm_fetch['bpms']) {
						$comp_percent = 100;
					} else {
						$comp_percent = round((($division_complete_fetch['mytotal']/$division_bpm_fetch['bpms']) * 100), 2);
					}
				}
				$svl_division_complete_query->execute(array(':division'=>$cluster['division'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
				$svl_division_complete_fetch = $svl_division_complete_query->fetch(PDO::FETCH_ASSOC);
				$svl_comp_percent = 0.00;
				if ($svl_division_complete_fetch['mytotal']==0) {
					$svl_comp_percent = 0;
				} else {
					if ($svl_division_complete_fetch['mytotal']>=$division_bpm_fetch['bpms']) {
						$svl_comp_percent = 100;
					} else {
						$svl_comp_percent = round((($svl_division_complete_fetch['mytotal']/$division_bpm_fetch['bpms']) * 100), 2);
					}
					
				}
				echo '				
								<tr data-id="'.$id_count.'" data-parent="0" data-level="1">
									<td data-column="name" class = "danger">Division: '.$cluster['division'].'</td>
									<td class = "danger">'.$division_bpm_fetch['bpms'].'</td>
									<td class = "danger">'.$division_complete_fetch['mytotal'].'</td>
									<td class = "danger">'.$comp_percent.'%</td>
									<td class = "danger">'.$svl_division_complete_fetch['mytotal'].'</td>
									<td class = "danger">'.$svl_comp_percent.'%</td>
								</tr>
				';
				$prev_division = $cluster['division'];
				$division_id = $id_count;
				$id_count++;
			}
			if ($cluster['region']!=$prev_region) {
				$region_complete_query->execute(array(':region'=>$cluster['region'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
				$region_complete_fetch = $region_complete_query->fetch(PDO::FETCH_ASSOC);
				$region_bpm_query->execute(array(':region'=>$cluster['region']));
				$region_bpm_fetch = $region_bpm_query->fetch(PDO::FETCH_ASSOC);
				$comp_percent = 0.00;
				if ($region_complete_fetch['mytotal']==0) {
					$comp_percent = 0;
				} else {
					if ($region_complete_fetch['mytotal']>=$region_bpm_fetch['bpms']) {
						$comp_percent = 100;
					} else {
						$comp_percent = round((($region_complete_fetch['mytotal']/$region_bpm_fetch['bpms']) * 100), 2);
					}
				}
				$svl_region_complete_query->execute(array(':region'=>$cluster['region'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
				$svl_region_complete_fetch = $svl_region_complete_query->fetch(PDO::FETCH_ASSOC);
				$svl_comp_percent = 0.00;
				if ($svl_region_complete_fetch['mytotal']==0) {
					$svl_comp_percent = 0;
				} else {
					if ($svl_region_complete_fetch['mytotal']>=$region_bpm_fetch['bpms']) {
						$svl_comp_percent = 100;
					} else {
						$svl_comp_percent = round((($svl_region_complete_fetch['mytotal']/$region_bpm_fetch['bpms']) * 100), 2);
					}
					
				}
				echo '				
								<tr data-id="'.$id_count.'" data-parent="'.$division_id.'" data-level="2">
									<td data-column="name" class = "success">Region: '.$cluster['region'].'</td>
									<td class = "success">'.$region_bpm_fetch['bpms'].'</td>
									<td class = "success">'.$region_complete_fetch['mytotal'].'</td>
									<td class = "success">'.$comp_percent.'%</td>
									<td class = "success">'.$svl_region_complete_fetch['mytotal'].'</td>
									<td class = "success">'.$svl_comp_percent.'%</td>
								</tr>
				';
				$prev_region = $cluster['region'];
				$region_id = $id_count;
				$id_count++;
			}
			if ($cluster['area']!=$prev_area) {
				$area_complete_query->execute(array(':area'=>$cluster['area'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
				$area_complete_fetch = $area_complete_query->fetch(PDO::FETCH_ASSOC);
				$area_bpm_query->execute(array(':area'=>$cluster['area']));
				$area_bpm_fetch = $area_bpm_query->fetch(PDO::FETCH_ASSOC);
				$comp_percent = 0.00;
				if ($area_complete_fetch['mytotal']==0) {
					$comp_percent = 0;
				} else {
					if ($area_complete_fetch['mytotal']>=$area_bpm_fetch['bpms']) {
						$comp_percent = 100;
					} else {
						$comp_percent = round((($area_complete_fetch['mytotal']/$area_bpm_fetch['bpms']) * 100), 2);
					}
				}
				$svl_area_complete_query->execute(array(':area'=>$cluster['area'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
				$svl_area_complete_fetch = $svl_area_complete_query->fetch(PDO::FETCH_ASSOC);
				$svl_comp_percent = 0.00;
				if ($svl_area_complete_fetch['mytotal']==0) {
					$svl_comp_percent = 0;
				} else {
					if ($svl_area_complete_fetch['mytotal']>=$area_bpm_fetch['bpms']) {
						$svl_comp_percent = 100;
					} else {
						$svl_comp_percent = round((($svl_area_complete_fetch['mytotal']/$area_bpm_fetch['bpms']) * 100), 2);
					}
					
				}
				echo '				
								<tr data-id="'.$id_count.'"  data-parent="'.$region_id.'" data-level="3">
									<td data-column="name" class = "warning">area: '.$cluster['area'].'</td>
									<td class = "warning">'.$area_bpm_fetch['bpms'].'</td>
									<td class = "warning">'.$area_complete_fetch['mytotal'].'</td>
									<td class = "warning">'.$comp_percent.'%</td>
									<td class = "warning">'.$svl_area_complete_fetch['mytotal'].'</td>
									<td class = "warning">'.$svl_comp_percent.'%</td>
								</tr>
				';
				$prev_area = $cluster['area'];
				$area_id = $id_count;
				$id_count++;
			}
			$cluster_complete_query->execute(array(':cluster'=>$cluster['cluster'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
			$cluster_complete_fetch = $cluster_complete_query->fetch(PDO::FETCH_ASSOC);
			$cluster_bpm_query->execute(array(':cluster'=>$cluster['cluster']));
			$cluster_bpm_fetch = $cluster_bpm_query->fetch(PDO::FETCH_ASSOC);
			$comp_percent = 0.00;
			if ($cluster_complete_fetch['mytotal']==0) {
				$comp_percent = 0;
			} else {
				if ($cluster_complete_fetch['mytotal']>=$cluster_bpm_fetch['total']) {
					$comp_percent = 100;
				} else {
					$comp_percent = round((($cluster_complete_fetch['mytotal']/$cluster_bpm_fetch['total']) * 100), 2);
				}
				
			}
			$svl_cluster_complete_query->execute(array(':cluster'=>$cluster['cluster'], ':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
			$svl_cluster_complete_fetch = $svl_cluster_complete_query->fetch(PDO::FETCH_ASSOC);
			$svl_comp_percent = 0.00;
			if ($svl_cluster_complete_fetch['mytotal']==0) {
				$svl_comp_percent = 0;
			} else {
				if ($svl_cluster_complete_fetch['mytotal']>=$cluster_bpm_fetch['total']) {
					$svl_comp_percent = 100;
				} else {
					$svl_comp_percent = round((($svl_cluster_complete_fetch['mytotal']/$cluster_bpm_fetch['total']) * 100), 2);
				}
				
			}
			echo '				
								<tr data-id="'.$id_count.'" data-parent="'.$area_id.'" data-level="4">
									<td data-column="name">cluster: '.$cluster['cluster'].'</td>
									<td>'.$cluster_bpm_fetch['total'].'</td>
									<td>'.$cluster_complete_fetch['mytotal'].'</td>
									<td>'.$comp_percent.'%</td>
									<td>'.$svl_cluster_complete_fetch['mytotal'].'</td>
									<td>'.$svl_comp_percent.'%</td>
								</tr>
			';
			
		}
		$id_count++;
		$national_complete_query->execute(array(':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
		$national_complete_fetch = $national_complete_query->fetch(PDO::FETCH_ASSOC);
		$national_bpm_query->execute();
		$national_bpm_fetch = $national_bpm_query->fetch(PDO::FETCH_ASSOC);
		$comp_percent = 0.00;
		if ($national_complete_fetch['mytotal']==0) {
			$comp_percent = 0;
		} else {
			if ($national_complete_fetch['mytotal']>=$national_bpm_fetch['bpms']) {
				$comp_percent = 100;
			} else {
				$comp_percent = round((($national_complete_fetch['mytotal']/$national_bpm_fetch['bpms']) * 100), 2);
			}
			
		}
		$svl_national_complete_query->execute(array(':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
		$svl_national_complete_fetch = $svl_national_complete_query->fetch(PDO::FETCH_ASSOC);
		$svl_comp_percent = 0.00;
		if ($svl_national_complete_fetch['mytotal']==0) {
			$svl_comp_percent = 0;
		} else {
			if ($svl_national_complete_fetch['mytotal']>=$national_bpm_fetch['bpms']) {
				$svl_comp_percent = 100;
			} else {
				$svl_comp_percent = round((($svl_national_complete_fetch['mytotal']/$national_bpm_fetch['bpms']) * 100), 2);
			}
			
		}
		echo '				
								<tr data-id="'.$id_count.'" data-parent="0" data-level="1">
									<td data-column="name" class = "info">National</td>
									<td class = "info">'.$national_bpm_fetch['bpms'].'</td>
									<td class = "info">'.$national_complete_fetch['mytotal'].'</td>
									<td class = "info">'.$comp_percent.'%</td>
									<td class = "info">'.$svl_national_complete_fetch['mytotal'].'</td>
									<td class = "info">'.$svl_comp_percent.'%</td>
								</tr>
		';


		echo '				</tbody>
						</table>
					</div>
					<div class="row">
						<br>
						<div class="col-xs-6">
							<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
							<br>
						</div>
						<div class="col-xs-6">
							<a href="report_xl.php?s='.$startDate.'&e='.$endDate.'" class="btn btn-primary btn-lg push-to-bottom pull-right" role="button">Save</a>
							<br>
						</div>
					</div>
		      		<br><br><br><br>
				</div>
			</body>
		</html>
		';
} else {
	header('Location: ' . $myPath . 'index.php?error=1');
}




/////////////////////////////////////////////

?>
