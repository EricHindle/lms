<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath .'includes/formkey.class.php';
	sec_session_start(); 
	if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
		$formKey = new formKey();
		$key = $formKey->outputKey();

		$weeksql = "SELECT * FROM lms_week ORDER BY lms_week_no ASC ";
		$weekquery = $mypdo->prepare($weeksql);
		$weekquery->execute();
		$weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);

		$html="";

		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Game Weeks</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
			    <script src="'.$myPath.'js/jquery.js"></script>
			    <script src="'.$myPath.'js/bootstrap.min.js"></script>
			    <script src="'.$myPath.'js/jquery.tablesorter.js"></script>
			    <script>
		            $(function(){
		            $(\'#keywords\').tablesorter(); 
		            });
		        </script>
			</head>

			<body>';
				include $myPath.'globNAV.php';
		$html.= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Week Admin</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

		$html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="addweek" method="post" action="add-week.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Add Week</h3>
				                    <div class="form-group">
				                    	<label for="weekyear">Year number:</label>
					                    <input type="text" class="form-control" id="weekyear" name="weekyear" placeholder="Year" />
				                    	<label for="weeknumber">Week number:</label>
					                    <input type="text" class="form-control" id="weeknumber" name="weeknumber" placeholder="Week" />
                                     	<label for="weekstart">Start date:</label>
                                        <input type="text" class="form-control" id="weekstart" name="weekstart" placeholder="yyyy-mm-dd" />
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

		$html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="editweek" method="post" action="edit-week.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Edit Value</h3>
				                    <div class="form-group">
			                        	<label for="user">Choose value:</label>
			                            <select class="form-control" id="weekid" name="weekid">';
		foreach ($weekfetch as $myweek) {
			$html.=						'<option value="'.$myweek['lms_week_id'].'">'.$myweek['lms_week_id'].'</option>';
		}
		$html .='	                    </select>
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

		
		$html .='		</div>
						<div class = "row">
				        	<div class="well col-md-7 col-md-offset-1 textDark">
				        		<h3>All weeks</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="week">
										<th>Week No.</th>
										<th>Year</th>
                                        <th>Start Date</th>
                                        <th>Pick Deadline</th>
                                        <th>End Date</th>
									</tr>
									</thead>
									<tbody>
									';
							foreach ($weekfetch as $rs) {
							    $stDate = date_format(date_create($rs['lms_week_start']),'d-m-Y');
							    $enDate = date_format(date_create($rs['lms_week_end_pick']),'d-m-Y');
							    $dlDate = date_format(date_create($rs['lms_week_deadline']),'d-m-Y');
							    
							    
							    
							    
								$html .='
									<tr>
										<td>' . $rs['lms_week'] . '</td>
										<td>' . $rs['lms_year'] . '</td>
										<td>' . $stDate . '</td>
										<td>' . $dlDate . '</td>
										<td>' . $enDate . '</td>
									</tr>';
							}
							$html .='
									</tbody>
								</table>
							</div>
						</div>

				        ';
		
		$html.= '	      		
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="'.$myPath.'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
							</div>
						</div>
			      		<br><br><br><br>
			    	</div>
			    </section>
			</body>
		</html>

		';
		echo $html;
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>
