<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access == 999) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            $gameid = $_SESSION['currentseason'] . $_SESSION['currentweek'] ;
                if ($gameid) {
                    $html = "";
                    $matchsql = "SELECT lms_match_id, lms_match_date, lms_match_result, lms_team_name FROM v_lms_match WHERE lms_match_weekno = :matchwk";
                    $matchquery = $mypdo->prepare($matchsql);
                    $matchquery->bindParam(':matchwk', $gameid);
                    $matchquery->execute();
                    $matchcount = $matchquery->rowCount();

                    $weeksql = "SELECT * FROM lms_week WHERE lms_week_no = :id LIMIT 1";
                    $weekquery = $mypdo->prepare($weeksql);
                    $weekquery->bindParam(':id', $gameid);
                    $weekquery->execute();
                    $weekcount = $weekquery->rowCount();

                    $week = 0;
                    $year = 0;
                    ;
                    if ($weekcount > 0) {
                        $key = $formKey->outputKey();
                        $remainingweeks = $weekquery->fetch(PDO::FETCH_ASSOC);
                        $week = $remainingweeks['lms_week'];
                        $year = $remainingweeks['lms_year'];
                    }

                    if ($matchcount > 0) {
                        $key = $formKey->outputKey();
                        $matchfetch = $matchquery->fetchAll(PDO::FETCH_ASSOC);
                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Enter Results</title>
									    
									    <meta name="viewport" content="width=device-width, initial-scale=1">
									    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
									    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
									    <script src="' . $myPath . 'js/jquery.js"></script>
									    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
									</head>

									<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
										<section id="homeSection">
									    <br><br>
									        <div class="container">
									        	<div class="row">
									                <div class="col-md-8">
									                    <h1><strong>Enter Results for Period ' . $year . '/' . sprintf('%02d', $week) . '</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

                        $html .= '			<div class="well col-md-8 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="gen" method="post" action="process-results.php">';
                        $html .= $key;
                        $html .= '					<h3 class="text-center">Results</h3>
     <div class="form-group">
					        	<table class="table table-bordered" id="matchtable">
									<thead>
									<tr class="match">
                                        <th>Team</th>
                                        <th>Match Date</th>
                                        <th>Result</th>
									</tr>
									</thead>
									<tbody>
									';

                        foreach ($matchfetch as $rs) {
                            $wresult = '';
                            $lresult = '';
                            $dresult = '';
                            $nresult = '';
                            switch ($rs['lms_match_result']) {
                                case 'w':
                                    $wresult = 'selected';
                                    break;
                                case 'l':
                                    $lresult = 'selected';
                                    break;
                                case 'd':
                                    $dresult = 'selected';
                                    break;
                                case '':
                                    $nresult = 'selected';
                                    break;
                            }
                            $md = date_create($matchfetch['lms_match_date']);
                            $kodate = date_format($md, 'd-M-Y');
                            $html .= '
									<tr>
                                        <td>' . $rs['lms_team_name'] . '</td>
										<td>' . $kodate . '</td>
                                       <td><select class="form-control" name="res-' . $rs['lms_match_id'] . '" id="res-' . $rs['lms_match_id'] . '">
                                            <option ' . $wresult . ' value="w">Win</option>
                                            <option ' . $dresult . ' value="d">Draw</option>
                                            <option ' . $lresult . ' value="l">Lose</option>
                                            <option ' . $lresult . ' value="p">Postponed</option>
                                            <option ' . $nresult . ' value="">No result</option>
                                       </select></tr>';
                        }
                        $html .= '
									</tbody>
								</table>
</div>
										                    <div class="form-group">
										                        <input type= "hidden" name= "weekid" value="' . $gameid . '" />
										                    </div>									                    	
										                    <div class="form-group">
										                    	<br>
										                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
										                    </div>
										                </form>
										            </div>
										        </div>
										        <div class="row">
													<br>
													<div class="col-xs-6">
														<a href="' . $myPath . 'struct/match/match-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
														<br>
													</div>
												</div>
									      		<br><br><br><br>
									    	</div>
									    </section>
									</body>
								</html>
									            ';
                    } else {
                        $html .= "<script>
										alert('There are no matches for this period.');
										window.location.href='match-main.php';
									</script>";
                    }

                    echo $html;
            } else {
                header('Location: ' . $myPath . 'index.php?error=1');
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>