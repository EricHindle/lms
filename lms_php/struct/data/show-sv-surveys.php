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

    $qsql = "SELECT questions_id from questions order by questions_id";
    $qquery =  $mypdo->prepare($qsql);
    $qquery->execute();
    $qcount = $qquery->rowCount();
    $qfetch = $qquery->fetchAll(PDO::FETCH_ASSOC);

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

			    <title>Smart Visit Data</title>

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
						<h2>Smart Visit</h2>
						<p>' . $startDate . '  to  ' . $endDate . '</p>
					</div>
				</div>';

    $sql1 = "SELECT * from svchkscomp WHERE datecomplete >= :dateStart AND datecomplete <= :dateEnd";
    $query1 = $mypdo->prepare($sql1);
    $query1->execute(array(
        ':dateStart' => $queryStartDate,
        ':dateEnd' => $queryEndDate
    ));
    $count1 = $query1->rowCount();
    if ($count1 > 0) {

        $fetch1 = $query1->fetchAll(PDO::FETCH_ASSOC);

        $html = '
				<table class="table table-striped table-bordered" id="keywords" >
					<thead>
					<tr>
                ';
        $firstrec = $fetch1[0];
        foreach ($firstrec as $key => $column) {
            if (substr($key, 0, 1) != "q") {

                $html .= '<th  class="info">' . $key . '</th>';
            }
        }
        foreach ($qfetch as $value){
            $html .= '<th  class="info">Q.' . $value['questions_id'] . '</th>';
        }
        
        $html .= '		</tr>
					</thead>
					<tbody>
					';
        echo $html;
        foreach ($fetch1 as $value) {
            echo '<tr>';
				foreach ($value as $cell) {
                        echo ' <td> ' . $cell . ' </td>';
                    }
                }
            }
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
                echo ' <td> ' . $ans . ' </td>';
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
					<a href="data-sv-date.php" class="btn btn-primary btn-lg" role="button">Back</a>
				</div>
				<div class="col-xs-6">
					<a href="data-sv-date-XL.php?s=' . $startDate . '&e=' . $endDate . '" class="btn btn-primary btn-lg  pull-right" target="_self" role="button">Save</a>
				</div>
			</div>
			<br><br>
		</div>
		';
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
