<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath .'includes/formkey.class.php';
	sec_session_start(); 
	if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
		$formKey = new formKey();
		$key = $formKey->outputKey();

		$areachangesql = "SELECT id, shop, cluster, active FROM lbos ORDER BY shop ASC";
		$areachangequery = $mypdo->prepare($areachangesql);
		$areachangequery->execute();
		$cafetch = $areachangequery->fetchAll(PDO::FETCH_ASSOC);


		$clusterSql = "SELECT DISTINCT cluster FROM lbos ORDER BY cluster ASC";
		$clusterQuery = $mypdo->prepare($clusterSql);
		$clusterQuery->execute();
		$clusterFetch = $clusterQuery->fetchAll(PDO::FETCH_ASSOC);

		$html="";

		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>LBO Admin</title>
			    
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
			                    <h1><strong>LBO Admin</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

		$html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="adduser" method="post" action="add-lbo.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Add LBO</h3>
			                    	<br>
				                    <div class="form-group">
				                    	<label for="lbonum">LBO Number (6 digits):</label>
				                    	<input type="text" class="form-control" id="lbonum" name="lbonum" maxlength="6" placeholder="LBO No." onkeyup="this.value=this.value.replace(/[^0-9]/g, \'\')" />
				                    	<label for="lboname">LBO Name:</label>
					                    <input type="text" class="form-control" id="lboname" name="lboname" placeholder="LBO name" /><br>
			                        	<label for="cluster">Cluster:</label>
			                            <select class="form-control" id="cluster" name="cluster">';
		foreach ($clusterFetch as $myCluster) {
			$html.=						'<option>'.$myCluster['cluster'].'</option>';
		}
		$html .='	                    </select>
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

		$html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="edituser" method="post" action="edit-lbo.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Edit LBO</h3>
			                    	<br>
				                    <div class="form-group">
			                        	<label for="user">Choose LBO:</label>
			                            <select class="form-control" id="user" name="user">';
		foreach ($cafetch as $myLbo) {
			$html.=						'<option value="'.$myLbo['id'].'">'.$myLbo['shop'].'</option>';
		}
		$html .='	                    </select>
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

		
		$html .='		</div>
						<div class = "row">
				        	<div class="well col-md-10 col-md-offset-1 textDark">
				        		<h3>All LBOs</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Name</th>
										<th>Cluster</th>
										<th>Active</th>
									</tr>
									</thead>
									<tbody>
									';
							foreach ($cafetch as $rs) {
								if ($rs['active']==1) {
									$active='Yes';
								} else {
									$active='No';
								}
								$html .='
									<tr>
										<td>' . $rs['shop'] . '</td>
										<td>' . $rs['cluster'] . '</td>
										<td>' . $active . '</td>
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
