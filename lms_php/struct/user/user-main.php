<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath .'includes/formkey.class.php';
	sec_session_start(); 
	if(login_check($mypdo) == true && $_SESSION['retaccess']==999) {
		$formKey = new formKey();
		$key = $formKey->outputKey();

		$areachangesql = "SELECT id, username, fname, division, region, area, cluster, role, active FROM members ORDER BY fname ASC";
		$areachangequery = $mypdo->prepare($areachangesql);
		$areachangequery->execute();
		$cafetch = $areachangequery->fetchAll(PDO::FETCH_ASSOC);


		$clusterSql = "SELECT DISTINCT cluster FROM lbos ORDER BY cluster ASC";
		$clusterQuery = $mypdo->prepare($clusterSql);
		$clusterQuery->execute();
		$clusterFetch = $clusterQuery->fetchAll(PDO::FETCH_ASSOC);

		$areaSql = "SELECT DISTINCT area FROM lbos ORDER BY area ASC";
		$areaQuery = $mypdo->prepare($areaSql);
		$areaQuery->execute();
		$areaFetch = $areaQuery->fetchAll(PDO::FETCH_ASSOC);

		$regionSql = "SELECT DISTINCT region FROM lbos ORDER BY region ASC";
		$regionQuery = $mypdo->prepare($regionSql);
		$regionQuery->execute();
		$regionFetch = $regionQuery->fetchAll(PDO::FETCH_ASSOC);

		$html="";

		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>User Admin</title>
			    
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
		        <script>
					$(document).ready(function () {
						toggleFields();
						$("#role").change(function () {
							toggleFields();
						});
					});
				</script>
				<script>
					function toggleFields() {
						switch ($("#role").val()) {
							case "BPM":
								$("#cluster").show();
								$("#lbl_cluster").show();
								$("#area").hide();
								$("#lbl_area").hide();
								$("#region").hide();
								$("#lbl_region").hide();
								$("#division").hide();
								$("#lbl_division").hide();
								break;
							case "AM":
								$("#cluster").hide();
								$("#lbl_cluster").hide();
								$("#area").show();
								$("#lbl_area").show();
								$("#region").hide();
								$("#lbl_region").hide();
								$("#division").hide();
								$("#lbl_division").hide();
								break;
							case "RM":
								$("#cluster").hide();
								$("#lbl_cluster").hide();
								$("#area").hide();
								$("#lbl_area").hide();
								$("#region").show();
								$("#lbl_region").show();
								$("#division").hide();
								$("#lbl_division").hide();
								break;
							case "DD":
								$("#cluster").hide();
								$("#lbl_cluster").hide();
								$("#area").hide();
								$("#lbl_area").hide();
								$("#region").hide();
								$("#lbl_region").hide();
								$("#division").show();
								$("#lbl_division").show();
								break;
							case "Admin":
								$("#cluster").hide();
								$("#lbl_cluster").hide();
								$("#area").hide();
								$("#lbl_area").hide();
								$("#region").hide();
								$("#lbl_region").hide();
								$("#division").hide();
								$("#lbl_division").hide();
								break;

						}
					}

					function validatePassReset(){
                            var canSubmit = false;
                            var password = $("#pwd1").val();
                            var confirmPassword = $("#pwd2").val();
                            if (password == confirmPassword) {
                            	if (password.length>7) {
                            		canSubmit = true;
                            	} else {
                            		alert("Password must contain at least 8 characters.");
                            		canSubmit = false;
                            	}
                            } else {
                                alert("Passwords do not match");
                            }

                            return canSubmit;
                    }

                    function checkPasswordMatch() {
                        var password = $("#pwd1").val();
                        var confirmPassword = $("#pwd2").val();
                        var element = document.getElementById("p1");
                        if (password.match(/[A-z]/) && password.match(/[A-Z]/) && password.match(/\d/) && password.length > 7)
                            document.getElementById("pwd1").style.borderColor = "green";
                        else
                            document.getElementById("pwd1").style.borderColor = "red";

                        if (password != confirmPassword)
                            document.getElementById("pwd2").style.borderColor = "red";
                        else {
                            document.getElementById("pwd2").style.borderColor = "green";
                            document.getElementById("pwd1").style.borderColor = "green";
                        }

                    }
                    $(document).ready(function () {
                        $("#pwd1, #pwd2").keyup(checkPasswordMatch);
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
			                    <h1><strong>User Admin</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

		$html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="adduser" method="post" action="add-user.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Add User</h3>
			                    	<br>
				                    <div class="form-group">
				                    	<label for="username">Username (no spaces or special characters. MAX 20):</label>
				                    	<input type="text" class="form-control" id="username" name="username" maxlength="20" placeholder="username" /><br>
				                    	<label for="password">Password (no spaces or special characters):</label>
					                    <input type="text" class="form-control" id="password" name="password" placeholder="password" /><br>
					                    <label for="fname">Full Name (no special characters):</label>
					                    <input type="text" class="form-control" id="fname" name="fname" placeholder="Full Name" /><br>
					                    <label for="email">Email (MUST BE AN @williamhill.co.uk ADDRESS):</label>
					                    <input type="text" class="form-control" name="email" id="email" placeholder="email"><br>
					                    <label for="role">Role:</label>
					                    <select class="form-control" id="role" name="role">
						                    <option>BPM</option>
						                    <option>AM</option>
						                    <option>RM</option>
						                    <option>DD</option>
						                    <option>Admin</option>
					                    </select>
					                    <br>';
		$html.='	                    <label id="lbl_division" for="division">Division:</label>
			                            <select class="form-control" id="division" name="division">
			                            	<option>North</option>
			                            	<option>South</option>
			                            </select>
		';

		$html.='	                    <label id="lbl_region" for="region">Region:</label>
			                            <select class="form-control" id="region" name="region">';
		foreach ($regionFetch as $myregion) {
			$html.=						'<option>'.$myregion['region'].'</option>';
		}
		$html .='	                    </select>
		';

		$html.='	                    <label id="lbl_area" for="area">Area:</label>
			                            <select class="form-control" id="area" name="area">';
		foreach ($areaFetch as $myarea) {
			$html.=						'<option>'.$myarea['area'].'</option>';
		}
		$html .='	                    </select>
		';

		$html.='	                    <label id="lbl_cluster" for="cluster">Cluster:</label>
			                            <select class="form-control" id="cluster" name="cluster">';
		foreach ($clusterFetch as $myCluster) {
			$html.=						'<option>'.$myCluster['cluster'].'</option>';
		}
		$html .='	                    </select>
			                            ';
		$html.='	                 
										<br>   
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

		$html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="edituser" method="post" action="edit-user.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Edit User</h3>
			                    	<br>
				                    <div class="form-group">
			                        	<label for="user">Choose User:</label>
			                            <select class="form-control" id="user" name="user">';
		foreach ($cafetch as $myUser) {
			$html.=						'<option value="'.$myUser['id'].'">'.$myUser['fname'].' <small>('.$myUser['username'].'   :'.$myUser['role'].')<small></option>';
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
			                	<form class="form-horizontal" role="form" name ="edituser" method="post" action="change-password.php" onsubmit="return validatePassReset()">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Change Password</h3>
									<h5>Password must contain at least 8 characters, including UPPERCASE, lowercase and numbers.</h5>
			                    	<br>
				                    <div class="form-group">
			                        	<label for="user">Choose User:</label>
			                            <select class="form-control" id="user" name="user">';
		foreach ($cafetch as $myUser) {
			$html.=						'<option value="'.$myUser['id'].'">'.$myUser['fname'].' <small>('.$myUser['username'].'   :'.$myUser['role'].')<small></option>';
		}
		$html .='	                    </select>
									</div>
                                    <br>
                                    <div class="form-group">
                                        <h4>New password:</h4>
                                        <input name="pwd1" id="pwd1" class="form-control" title="Password must contain at least 8 characters, including UPPERCASE, lowercase and numbers." type="password" onChange="checkPasswordMatch()">
                                    </div>
                                    <div class="form-group">
                                        <h4>Confirm password:</h4>
                                        <input id="pwd2" name="pwd2" class="form-control" title="Please enter the same Password as above." type="password" onChange="checkPasswordMatch()">
                                        <br>
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
				        		<h3>All Users</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Username</th>
										<th>Name</th>
										<th>Division</th>
										<th>Region</th>
										<th>Area</th>
										<th>Cluster</th>
										<th>Role</th>
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
										<td>' . $rs['username'] . '</td>
										<td>' . $rs['fname'] . '</td>
										<td>' . $rs['division'] . '</td>
										<td>' . $rs['region'] . '</td>
										<td>' . $rs['area'] . '</td>
										<td>' . $rs['cluster'] . '</td>
										<td>' . $rs['role'] . '</td>
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
