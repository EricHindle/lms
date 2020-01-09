<?php
$myPath='../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
sec_session_start(); 
if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
	echo '
	<!doctype html>
	<html>
		<head>

		    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
		    <meta charset="UTF-8">

		    <title>SV File Upload</title>

		    <meta name="viewport" content="width=device-width, initial-scale=1">
		    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
		    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
		    <script src="' . $myPath . 'js/jquery.js"></script>
		    <script src="' . $myPath . 'js/bootstrap.min.js">
		    </script><script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
		    <script>
	            $(function(){
	            $(\'#keywords\').tablesorter(); 
	            });
	        </script>
		    
		</head>

		<body>
	';
	include $myPath . 'globNAV.php';
	echo '
			<br><br><br><br>
			<div class = "container">
				<section id="veform">
	';

	if (empty($_FILES)) {
		echo '

					<div class="well well-xs col-xs-12">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h2>File upload</h2>
								<h5>Make sure spreadsheet has employee number column removed, cluster number is on the third column and data starts on row 2 (headings row 1)</h5>
								<h5>1. Click choose file and select spreadsheet</h5>
								<h5>2. Click upload</h5>
								<br>
								<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="uload.php">
									<label class="btn btn-success btn-lg">Choose file
										<input class="form-control" type="file" name="holly" hidden>
									</label>
									<br><br><br><br>
									<button type="submit" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-upload"></span>Upload</button>
								</form>
							</div>
						</div>
						<div class="row">
						<br>
						<div class="col-xs-6">
							<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg" role="button">Back</a>
						</div>
						<br><br>
					</div>

		';
	} else {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $_FILES['holly']['tmp_name']);
		finfo_close($finfo);
		if ($mime!='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
			echo '
						<div class="well well-xs col-xs-12">
							<div class="row">
								<div class="col-xs-12 text-center">
									<h2>Oops. File format not recognised.</h2>
									<h3>Make sure file is .xlsx</h3>
								</div>
							</div>
							<div class="row">
								<br>
								<div class="col-xs-6">
									<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								</div>
							</div>
						</div>
			';
		} else {
		
			require_once $myPath . 'includes/phpexcel/PHPExcel.php';
			//Load excel file using PHPExcel's iofactory
			$excel = PHPExcel_IOFactory::load($_FILES['holly']['tmp_name']);

			//Set active sheet to first sheet
			$excel->setActiveSheetIndex(0);

			//RESET table
			$get_ids_sql = "SELECT id FROM bpm_comp_rates";
			$get_ids_query = $mypdo->prepare($get_ids_sql);
			$get_ids_query->execute();
			$get_ids_fetch = $get_ids_query->fetchAll(PDO::FETCH_ASSOC);
			$set_ids_sql = "UPDATE bpm_comp_rates SET total = 0 WHERE id = :id";
			$set_ids_query = $mypdo->prepare($set_ids_sql);
			foreach ($get_ids_fetch as $row) {
				$set_ids_query->execute(array(':id'=>$row['id']));
			}

			//Update query
			$updateSql = "UPDATE bpm_comp_rates SET total = :total WHERE cluster = :cluster";
			$updateQuery = $mypdo->prepare($updateSql);
			
			echo '
						<div class="well well-xs col-xs-12">
							<div class="row">
								<div class="col-xs-12 text-center">
									<h2>Table Check</h2>
									<h5>If required, check that the figures look OK. Click next (below table) to continue</h5>
									<h5>Table can be sorted by clicking on headings</h5>
									<br>
									<table class="table table-striped table-bordered" id="keywords" >
										<thead>
											<tr>
												<th class = "info text-center">Cluster</th>
												<th class = "info text-center">CEMs</th>
											</tr>
										</thead>
										<tbody>
			';

			//first row of data series
			$i = 2;

			//array for total cluster cem's
			$cems = array();

			//loop until end of data series (cell contains empty string)
			while ($excel->getActiveSheet()->getCell('A'.$i)->getValue() != "") {
				//get cells value
				// $id = $excel->getActiveSheet()->getCell('A'.$i)->getValue();
				// $region = $excel->getActiveSheet()->getCell('B'.$i)->getValue();
				// $area = $excel->getActiveSheet()->getCell('C'.$i)->getValue();
				$cluster = $excel->getActiveSheet()->getCell('C'.$i)->getValue();

				// echo '
				// 	<tr>
				// 		<td>'.$id.'</td>
				// 		<td>'.$region.'</td>
				// 		<td>'.$area.'</td>
				// 		<td>'.$cluster.'</td>
				// 	</tr>
				// ';

				//trim leading 0
				$cluster = $str = ltrim($cluster, '0');

				//Check if cluster key exists in $cems array
				if (array_key_exists($cluster,$cems)){
					$cems[$cluster]++;
				}
				else
				{
					$cems[$cluster] = 1;
				}

				$i++;
			}

			foreach ($cems as $key => $value) {

				$updateQuery->execute(array(':cluster'=>$key, ':total'=>$value));
				echo '
												<tr>
													<td>'.$key.'</td>
													<td>'.$value.'</td>
												</tr>
				';
			}

			echo '
										</tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<br>
								<div class="col-xs-6">
									<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg" role="button">Back</a>
								</div>
								<div class="col-xs-6">
									<a href="uload_date.php" class="btn btn-primary btn-lg  pull-right" target="_self" role="button">Next</a>
								</div>
							</div>
						</div>
						<br><br>
			';
		}
	}

	echo '
				</section>
			</div>
		</body>
	</html>
	';
} else { 
        header('Location: '.$myPath.'index.php?error=1');
}


?>
