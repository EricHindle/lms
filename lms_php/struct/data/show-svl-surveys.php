<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
sec_session_start();

if (login_check($mypdo) == true && $_SESSION['retaccess'] == 999) {
	
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



	echo '
		<!doctype html>
		<html>
			<head>

			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">

			    <title>Smart Visit Lite Data</title>

			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/retmanage.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			    
			</head>

			<body id="backWhite">';
	include $myPath . 'globNAV.php';
	echo '	<div class="container-fluid">
				<br><br><br><br>

				<br>
				<div class="row">
					<div class = "jumbotron text-center">
						<h2>Smart Visit Lite</h2>
						<p>' . $startDate . '  to  ' . $endDate . '</p>
					</div>
				</div>';

	$sql1 = "SELECT * from svlitechkscomp WHERE datecomplete >= :dateStart AND datecomplete <= :dateEnd";
	$query1 = $mypdo->prepare($sql1);
	$query1->execute(array(':dateStart' => $queryStartDate, ':dateEnd' => $queryEndDate));
	$count1 = $query1->rowCount();
	if ($count1>0) {

		$fetch1 = $query1->fetchAll(PDO::FETCH_ASSOC);

		echo '
				<table class="table table-striped table-bordered" id="keywords" >
					<thead>
					<tr>
						<th  class="info">id</th>
						<th  class="info">num</th>
						<th  class="info">shop</th>
						<th  class="info">division</th>
						<th  class="info">region</th>
						<th  class="info">area</th>
						<th  class="info">cluster</th>
						<th  class="info">datestart</th>
						<th  class="info">datecomplete</th>
						<th  class="info">completedby</th>
						<th  class="info">cem</th>
						<th  class="info">score</th>
						<th  class="info">na</th>
						<th  class="info">lat</th>
						<th  class="info">lon</th>
						<th  class="info">q1</th>
						<th  class="info">q2</th>
						<th  class="info">q3</th>
						<th  class="info">q4</th>
						<th  class="info">q5</th>
						<th  class="info">q6</th>
						<th  class="info">q7</th>
						<th  class="info">q8</th>
						<th  class="info">q9</th>
						<th  class="info">q10</th>
						<th  class="info">q11</th>
						<th  class="info">q12</th>
						<th  class="info">q13</th>
						<th  class="info">q14</th>
						<th  class="info">q15</th>
						<th  class="info">q16</th>
						<th  class="info">q17</th>
						<th  class="info">q18</th>
						<th  class="info">q19</th>
						<th  class="info">q20</th>
						<th  class="info">q21</th>
						<th  class="info">q22</th>
						<th  class="info">q23</th>
						<th  class="info">q24</th>
						<th  class="info">q25</th>
						<th  class="info">q26</th>
						<th  class="info">q27</th>
						<th  class="info">q28</th>
						<th  class="info">q29</th>
						<th  class="info">q30</th>
						<th  class="info">s1</th>
						<th  class="info">s2</th>
						<th  class="info">s3</th>
						<th  class="info">s4</th>
						<th  class="info">s5</th>
						<th  class="info">s6</th>
						<th  class="info">s7</th>
						<th  class="info">comment1</th>
						<th  class="info">comment2</th>
						<th  class="info">comment3</th>
						<th  class="info">comment4</th>
						<th  class="info">comment5</th>
						<th  class="info">comment6</th>
						<th  class="info">comment7</th>
						<th  class="info">veperiod</th>
					</tr>
					</thead>
					<tbody>
					';

			foreach ($fetch1 as $value) {
			echo '<tr>';
				foreach ($value as $cell) {
					echo' <td> '.$cell.' </td>';
				}

			echo '</tr>';
			}
			echo '	</tbody>
				</table>';
	} else {
		echo '
			<div class="row">
				<div class = "jumbotron text-center">
					<h2>No data found.</h2>
				</div>
			</div>';
	}
	

	echo '
			<div class="row">
				<br>
				<div class="col-xs-6">
					<a href="data-svl-date.php" class="btn btn-primary btn-lg" role="button">Back</a>
				</div>
				<div class="col-xs-6">
					<a href="data-svl-date-XL.php?s='.$startDate.'&e='.$endDate.'" class="btn btn-primary btn-lg  pull-right" target="_self" role="button">Save</a>
				</div>
			</div>
			<br><br>
		</div>
		';
} else {
	header('Location: ' . $myPath . 'index.php?error=1');
}
?>
