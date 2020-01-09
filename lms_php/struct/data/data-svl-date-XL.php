<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/export-xls.class.php';
sec_session_start();

if (login_check($mypdo) == true && $_SESSION['retaccess']==999) {
	if (isset($_GET['s'], $_GET['e'])) {
		$startDate = $_GET['s'];
		$endDate = $_GET['e'];
		if ($startDate && $endDate) {

			$queryEndDate = date_format(date_create_from_format('d/m/Y', $endDate), 'Y-m-d');
			$queryStartDate = date_format(date_create_from_format('d/m/Y', $startDate), 'Y-m-d');
			$queryEndDate = $queryEndDate . ' 23:59:59';
			$queryStartDate = $queryStartDate . ' 00:00:00';


			$filename = 'Smart Visit Lite Export.xls';
			$xls = new ExportXLS($filename);
			$header[] = 'Smart Visit Lite - '.$startDate.' to '.$endDate;
			$xls->addHeader($header);
			$header = null;


			$sql1 = "SELECT * from svlitechkscomp WHERE datecomplete >= :dateStart AND datecomplete <= :dateEnd";
			$query1 = $mypdo->prepare($sql1);
			$query1->execute(array(':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
			$count1 = $query1->rowCount();


			if ($count1>0) {

				$fetch1 = $query1->fetchAll(PDO::FETCH_ASSOC);
				

				$xls->addRow(' ');
				$header = null;
				$header[] = "id";
				$header[] = "num";
				$header[] = "shop";
				$header[] = "division";
				$header[] = "region";
				$header[] = "area";
				$header[] = "cluster";
				$header[] = "datestart";
				$header[] = "datecomplete";
				$header[] = "completedby";
				$header[] = "cem";
				$header[] = "score";
				$header[] = "na";
				$header[] = "lat";
				$header[] = "lon";
				$header[] = "q1";
				$header[] = "q2";
				$header[] = "q3";
				$header[] = "q4";
				$header[] = "q5";
				$header[] = "q6";
				$header[] = "q7";
				$header[] = "q8";
				$header[] = "q9";
				$header[] = "q10";
				$header[] = "q11";
				$header[] = "q12";
				$header[] = "q13";
				$header[] = "q14";
				$header[] = "q15";
				$header[] = "q16";
				$header[] = "q17";
				$header[] = "q18";
				$header[] = "q19";
				$header[] = "q20";
				$header[] = "q21";
				$header[] = "q22";
				$header[] = "q23";
				$header[] = "q24";
				$header[] = "q25";
				$header[] = "q26";
				$header[] = "q27";
				$header[] = "q28";
				$header[] = "q29";
				$header[] = "q30";
				$header[] = "s1";
				$header[] = "s2";
				$header[] = "s3";
				$header[] = "s4";
				$header[] = "s5";
				$header[] = "s6";
				$header[] = "s7";
				$header[] = "comment1";
				$header[] = "comment2";
				$header[] = "comment3";
				$header[] = "comment4";
				$header[] = "comment5";
				$header[] = "comment6";
				$header[] = "comment7";
				$header[] = "veperiod";
				$xls->addRow($header);
				$header = null;

				foreach ($fetch1 as $value) {
					$myRow = array();
					$myRow[] = $value['id'];
					$myRow[] = $value['num'];
					$myRow[] = $value['shop'];
					$myRow[] = $value['division'];
					$myRow[] = $value['region'];
					$myRow[] = $value['area'];
					$myRow[] = $value['cluster'];
					$myRow[] = $value['datestart'];
					$myRow[] = $value['datecomplete'];
					$myRow[] = $value['completedby'];
					$myRow[] = $value['cem'];
					$myRow[] = $value['score'];
					$myRow[] = $value['na'];
					$myRow[] = $value['lat'];
					$myRow[] = $value['lon'];
					$myRow[] = $value['q1'];
					$myRow[] = $value['q2'];
					$myRow[] = $value['q3'];
					$myRow[] = $value['q4'];
					$myRow[] = $value['q5'];
					$myRow[] = $value['q6'];
					$myRow[] = $value['q7'];
					$myRow[] = $value['q8'];
					$myRow[] = $value['q9'];
					$myRow[] = $value['q10'];
					$myRow[] = $value['q11'];
					$myRow[] = $value['q12'];
					$myRow[] = $value['q13'];
					$myRow[] = $value['q14'];
					$myRow[] = $value['q15'];
					$myRow[] = $value['q16'];
					$myRow[] = $value['q17'];
					$myRow[] = $value['q18'];
					$myRow[] = $value['q19'];
					$myRow[] = $value['q20'];
					$myRow[] = $value['q21'];
					$myRow[] = $value['q22'];
					$myRow[] = $value['q23'];
					$myRow[] = $value['q24'];
					$myRow[] = $value['q25'];
					$myRow[] = $value['q26'];
					$myRow[] = $value['q27'];
					$myRow[] = $value['q28'];
					$myRow[] = $value['q29'];
					$myRow[] = $value['q30'];
					$myRow[] = $value['s1'];
					$myRow[] = $value['s2'];
					$myRow[] = $value['s3'];
					$myRow[] = $value['s4'];
					$myRow[] = $value['s5'];
					$myRow[] = $value['s6'];
					$myRow[] = $value['s7'];
					$myRow[] = $value['comment1'];
					$myRow[] = $value['comment2'];
					$myRow[] = $value['comment3'];
					$myRow[] = $value['comment4'];
					$myRow[] = $value['comment5'];
					$myRow[] = $value['comment6'];
					$myRow[] = $value['comment7'];
					$myRow[] = $value['veperiod'];

					$xls->addRow($myRow);
				}
				$xls->sendFile();
			} else {
				$xls->addRow(' ');
				$header = null;
				$header[] = "NO DATA";
				$xls->addRow($header);
				$xls->sendFile();
			}
		}
	}
}
?>
