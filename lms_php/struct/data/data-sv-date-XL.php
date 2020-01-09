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

			$qsql = "SELECT questions_id from questions order by questions_id";
			$qquery =  $mypdo->prepare($qsql);
			$qquery->execute();
			$qcount = $qquery->rowCount();
			$qfetch = $qquery->fetchAll(PDO::FETCH_ASSOC);
			
			$filename = 'Smart Visit Export.xls';
			$xls = new ExportXLS($filename);
			$header[] = 'Smart Visit - '.$startDate.' to '.$endDate;
			$xls->addHeader($header);
			$header = null;


			$sql1 = "SELECT * from svchkscomp WHERE datecomplete >= :dateStart AND datecomplete <= :dateEnd";
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
				$header[] = "s1";
				$header[] = "s2";
				$header[] = "s3";
				$header[] = "s4";
				$header[] = "s5";
				$header[] = "s6";
				$header[] = "s7";
				$header[] = "s8";
				$header[] = "s9";
				$header[] = "comment1";
				$header[] = "comment2";
				$header[] = "comment3";
				$header[] = "comment4";
				$header[] = "comment5";
				$header[] = "comment6";
				$header[] = "comment7";
				$header[] = "comment8";
				$header[] = "comment9";
				$header[] = "veperiod";
				foreach ($qfetch as $value){
				    $header[] = "Q." . $value['questions_id'];
				}
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
					$myRow[] = $value['s1'];
					$myRow[] = $value['s2'];
					$myRow[] = $value['s3'];
					$myRow[] = $value['s4'];
					$myRow[] = $value['s5'];
					$myRow[] = $value['s6'];
					$myRow[] = $value['s7'];
					$myRow[] = $value['s8'];
					$myRow[] = $value['s9'];
					$myRow[] = $value['comment1'];
					$myRow[] = $value['comment2'];
					$myRow[] = $value['comment3'];
					$myRow[] = $value['comment4'];
					$myRow[] = $value['comment5'];
					$myRow[] = $value['comment6'];
					$myRow[] = $value['comment7'];
					$myRow[] = $value['comment8'];
					$myRow[] = $value['comment9'];
					$myRow[] = $value['veperiod'];

					$aarray = array_fill(0,$qcount," ");
					$sql3 = "SELECT * from answers WHERE svchkscomp_id = :compid";
					$query3 = $mypdo->prepare($sql3);
					$query3->execute(array(':compid' => $value['id']));
					$count3 = $query3->rowCount();
					if ($count3 > 0){
					    $fetch3 = $query3->fetchAll(PDO::FETCH_ASSOC);
					    foreach ($fetch3 as $answer) {
					        for ($a = 0; $a < $qcount; $a++){
					            $quest = $qfetch[$a];
					            if ($quest['questions_id'] ==  $answer['questions_id']) {
					                $aarray[$a] = $answer['answer_value'];
					            }
					        }
					    }
					}
					
					foreach($aarray as $ans){
					    $myRow[] = $ans ;
					}
					
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
