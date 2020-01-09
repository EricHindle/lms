<?php
$myPath='../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
sec_session_start(); 
if (login_check($mypdo) == true && $_SESSION['retaccess'] == 999) {
	if (isset($_GET['s'], $_GET['e'])) {
		$startDate = $_GET['s'];
		$endDate = $_GET['e'];
		if ($startDate && $endDate) {
			require_once $myPath . 'includes/phpexcel/PHPExcel.php';

			//create PHPExcel object
			$excel = new PHPExcel();

			//insert some data to PHPExcel object
			$excel->setActiveSheetIndex(0)
			 ->setCellValue('A1',$startDate.' - '.$endDate)
			 ->setCellValue('A2','')
			 ->setCellValue('B2','Colleagues')
			 ->setCellValue('C2','SV Complete')
			 ->setCellValue('D2','SV Percentage')
			 ->setCellValue('E2','SVL Complete')
			 ->setCellValue('F2','SVL Percentage');


			$i=3;
			/////////////////////////////////////////
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
			$prev_division = "";
			$prev_region = "";
			$prev_area = "";
			foreach ($cluster_fetch as $cluster) {
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
					$excel->setActiveSheetIndex(0)
					->setCellValue('A'.$i,'Division: '.$cluster['division'])
					->setCellValue('B'.$i,$division_bpm_fetch['bpms'])
					->setCellValue('C'.$i,$division_complete_fetch['mytotal'])
					->setCellValue('D'.$i,$comp_percent.'%')
					->setCellValue('E'.$i,$svl_division_complete_fetch['mytotal'])
					->setCellValue('F'.$i,$svl_comp_percent.'%');
					$prev_division = $cluster['division'];
					$i++;
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
					$excel->setActiveSheetIndex(0)
					->setCellValue('A'.$i,'Region: '.$cluster['region'])
					->setCellValue('B'.$i,$region_bpm_fetch['bpms'])
					->setCellValue('C'.$i,$region_complete_fetch['mytotal'])
					->setCellValue('D'.$i,$comp_percent.'%')
					->setCellValue('E'.$i,$svl_region_complete_fetch['mytotal'])
					->setCellValue('F'.$i,$svl_comp_percent.'%');
					$prev_region = $cluster['region'];
					$i++;
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
					$excel->setActiveSheetIndex(0)
					->setCellValue('A'.$i,'Area: '.$cluster['area'])
					->setCellValue('B'.$i,$area_bpm_fetch['bpms'])
					->setCellValue('C'.$i,$area_complete_fetch['mytotal'])
					->setCellValue('D'.$i,$comp_percent.'%')
					->setCellValue('E'.$i,$svl_area_complete_fetch['mytotal'])
					->setCellValue('F'.$i,$svl_comp_percent.'%');
					$prev_area = $cluster['area'];
					$i++;
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
			$excel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,$cluster['cluster'])
			->setCellValue('B'.$i,$cluster_bpm_fetch['total'])
			->setCellValue('C'.$i,$cluster_complete_fetch['mytotal'])
			->setCellValue('D'.$i,$comp_percent.'%')
			->setCellValue('E'.$i,$svl_cluster_complete_fetch['mytotal'])
			->setCellValue('F'.$i,$svl_comp_percent.'%');
			$i++;
			}//END FOREACH
				
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
			$excel->setActiveSheetIndex(0)
			->setCellValue('A'.$i,'National')
			->setCellValue('B'.$i,$national_bpm_fetch['bpms'])
			->setCellValue('C'.$i,$national_complete_fetch['mytotal'])
			->setCellValue('D'.$i,$comp_percent.'%')
			->setCellValue('E'.$i,$svl_national_complete_fetch['mytotal'])
			->setCellValue('F'.$i,$svl_comp_percent.'%');

			////////////////////////////////////


			//redirect to browser (download) instead of saving the result as a file

			//this is for MS Office Excel 2007 xlsx format
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="completion_report.xlsx"');

			//this is for MS Office Excel 2003 xls format
			//header('Content-Type: application/vnd.ms-excel');
			//header('Content-Disposition: attachment; filename="test.xlsx"');


			header('Cache-Control: max-age=0');

			//write the result to a file
			//for excel 2007 format
			$file = PHPExcel_IOFactory::createWriter($excel,'Excel2007');

			//for excel 2003 format
			//$file = PHPExcel_IOFactory::createWriter($excel,'Excel5');

			//output to php output instead of filename
			$file->save('php://output');
		}
	}
} else {
	header('Location: ' . $myPath . 'index.php?error=1');
}



/////////////////////////////////////////////

?>
