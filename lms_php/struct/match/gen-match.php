<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['weekid'])) {
                $gameid = $_POST['weekid'];
                if ($gameid) {
                    $html = "";
                    $teamsql = "SELECT lms_team_name, lms_team_id, lms_team_active FROM lms_team WHERE lms_team_active = 1 ORDER BY lms_team_name";
                    $teamquery = $mypdo->prepare($teamsql);
                    $teamquery->execute();
                    $teamcount = $teamquery->rowCount();

                    $weeksql = "SELECT * FROM lms_week WHERE lms_week_no = :id LIMIT 1";
                    $weekquery = $mypdo->prepare($weeksql);
                    $weekquery->bindParam(':id', $gameid);
                    $weekquery->execute();
                    $weekcount = $weekquery->rowCount();

                    $week = 0;
                    $year = 0;
                    $kodate = '';
                    if ($weekcount > 0) {
                        $key = $formKey->outputKey();
                        $remainingweeks = $weekquery->fetch(PDO::FETCH_ASSOC);
                        $week = $remainingweeks['lms_week'];
                        $year = $remainingweeks['lms_year'];
                        $md = date_create($remainingweeks['lms_week_start']);
                        $md->modify('next saturday');
                        $kodate = date_format($md, 'Y-m-d');
                    }

                    if ($teamcount > 0) {
                        $key = $formKey->outputKey();
                        $teamfetch = $teamquery->fetchAll(PDO::FETCH_ASSOC);
                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Generate Matches</title>
									    
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
									                <div class="col-md-7">
									                    <h1><strong>Generate Matches for Period ' . $year . '/' . $week . '</strong></h1>
									                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/match/match-main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>


									      		</div>
									        	<div class = "row">';

                        $html .= '			<div class="well col-md-8 textDark">
									                	<form class="form-horizontal" role="form" name ="gen" method="post" action="process-gen-match.php">';
                        $html .= $key;
                        $html .= '					<h3 class="text-center">Matches</h3>
     <div class="form-group">
					        	<table class="table table-bordered" id="matchtable">
									<thead>
									<tr class="match">
										<th>Select</th>
                                        <th>Team</th>
                                        <th>Match Date</th>
									</tr>
									</thead>
									<tbody>
									';

                        foreach ($teamfetch as $rs) {
                            $matchsql = "SELECT * FROM lms_match WHERE lms_match_weekno = :id and lms_match_team = :team LIMIT 1";
                            $matchquery = $mypdo->prepare($matchsql);
                            $matchquery->bindParam(':id', $gameid);
                            $matchquery->bindParam(':team', $rs['lms_team_id']);
                            $matchquery->execute();
                            $matchcount = $matchquery->rowCount();
                            if ($matchcount == 0) {

                                $html .= '
									<tr>
										<td><input type="checkbox" style="margin-left:20px;" name="add-' . $rs['lms_team_id'] . '" id="add-' . $rs['lms_team_id'] . '" value="true" ></td>
                                        <td>' . $rs['lms_team_name'] . '</td>
										<td><input type="text" class="form-control" id="mdt-' . $rs['lms_team_id'] . '" name="mdt-' . $rs['lms_team_id'] . '" value="' . $kodate . '" placeholder="yyyy-mm-dd"></td>
									</tr>';
                            }
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
										alert('There was a problem. Please check details and try again.');
										window.location.href='match-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='match-main.php';
									</script>";
                }
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