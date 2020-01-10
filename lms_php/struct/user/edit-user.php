<?php
	$myPath='../../';

	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';

	sec_session_start();
	$formKey = new formKey();
	$access = sanitize_int($_SESSION['retaccess']);
	if(login_check($mypdo) == true && $access == 999) {
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
	        if(!isset($_POST['form_key']) || !$formKey->validate())
	        {
	            header('Location: '.$myPath.'index.php?error=1');
	        }
	        else
	        {
	            if (isset($_POST['user']))
	            {
	                $id = sanitize_int($_POST['user']);
	                if($id)
	                {

	                	$html="";
	                	$usersql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access FROM lms_player WHERE lms_player_id = :id";
						$userquery = $mypdo->prepare($usersql);
						$userquery->execute(array(':id' => $id));
						$usercount = $userquery->rowCount();

						if( $usercount>0){
							$clusterSql = "SELECT DISTINCT cluster FROM lbos ORDER BY cluster ASC";
							$clusterQuery = $mypdo->prepare($clusterSql);
							$clusterQuery->execute();
							$clusterFetch = $clusterQuery->fetchAll(PDO::FETCH_ASSOC);
							$roleSql = "SELECT DISTINCT role FROM members ORDER BY role ASC";
							$roleQuery = $mypdo->prepare($roleSql);
							$roleQuery->execute();
							$roleFetch = $roleQuery->fetchAll(PDO::FETCH_ASSOC);
							$key = $formKey->outputKey();
							$userfetch=$userquery->fetch(PDO::FETCH_ASSOC);
							echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Edit User</title>
									    
									    <meta name="viewport" content="width=device-width, initial-scale=1">
									    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
									    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
									    <script src="'.$myPath.'js/jquery.js"></script>
									    <script src="'.$myPath.'js/bootstrap.min.js"></script>
									</head>

									<body>';
										include $myPath.'globNAV.php';
								$html.= '
										<section id="homeSection">
									    <br><br>
									        <div class="container">
									        	<div class="row">
									                <div class="col-md-12">
									                    <h1><strong>Edit cluster/active</strong></h1>
									                    <br>
									                </div>
									      		</div>
									        	<div class = "row">';

								$html .= '			<div class="well col-md-10 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-user.php">';
								$html .= $key;
								$html .= '					<h3 class="text-center">Edit User</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="control-label col-sm-2" for="name">Name:</label>
																<div class="col-sm-2">
																 	<p class="form-control-static" name="name">'.$userfetch['fname'].'</p>
																</div>
																<label class="control-label col-sm-2" for="uname">Username:</label>
																<div class="col-sm-2">
																 	<p class="form-control-static" name="uname">'.$userfetch['username'].'</p>
																</div>
																<label class="control-label col-sm-2" for="orole">Role:</label>
																<div class="col-sm-2">
																 	<p class="form-control-static" name="orole">'.$userfetch['role'].'</p>
																</div>
															</div>
															<div class="form-group">
																<label class="control-label col-sm-2" for="odivision">Division:</label>
																<div class="col-sm-1">
																 	<p class="form-control-static" name="odivision">'.$userfetch['division'].'</p>
																</div>
																<label class="control-label col-sm-2" for="oregion">Region:</label>
																<div class="col-sm-1">
																 	<p class="form-control-static" name="oregion">'.$userfetch['region'].'</p>
																</div>
																<label class="control-label col-sm-2" for="oarea">Area:</label>
																<div class="col-sm-1">
																 	<p class="form-control-static" name="oarea">'.$userfetch['area'].'</p>
																</div>
																<label class="control-label col-sm-2" for="ocluster">Cluster:</label>
																<div class="col-sm-1">
																 	<p class="form-control-static" name="ocluster">'.$userfetch['cluster'].'</p>
																</div>
															</div>
															<input type= "hidden" name= "id" value="'.$id.'" />
															<div class="form-group">
																<label class="control-label col-sm-2" for="oactive">Current Status:</label>
																<div class="col-sm-10">';
									if ($userfetch['active']==1) {
										$html.='					<p class="form-control-static" name="ooactive">Active</p>';
									}else{
										$html.='					<p class="form-control-static" name="ooactive">Inactive</p>';
									}

									$html.='					</div>
															</div>
															<div class = "form-group">
																<label class="control-label col-sm-2" for="role">New Role:</label>
																<div class="col-sm-10">
												                    <select class="form-control" id="role" name="role">';
									foreach ($roleFetch as $myRole) {
										if ($myRole['role']==$userfetch['role']) {
											$html.=						'<option selected="selected">'.$myRole['role'].'</option>';
										}else{
											$html.=						'<option>'.$myRole['role'].'</option>';
										}
									}
									$html.='			            </select>
												                </div>
															</div>
										                    <div class="form-group">
										                    	<label class="control-label col-sm-2" for="cluster"> New cluster:</label>
										                    	<div class="col-sm-10">
										                            <select class="form-control" id="cluster" name="cluster">';
									foreach ($clusterFetch as $myCluster) {
										if ($myCluster['cluster']==$userfetch['cluster']) {
											$html.=						'<option selected="selected">'.$myCluster['cluster'].'</option>';
										}else{
											$html.=						'<option>'.$myCluster['cluster'].'</option>';
										}
									}
									$html .='	                    </select>
																</div>
										                    </div>
										                    <div class="form-group">
										                    	<label class="control-label col-sm-2" for="active"> New status:</label>
										                    	<div class="col-sm-10">
										                    		<select class="form-control" id="active" name="active">';
	                    			if ($userfetch['active']==1) {
											$html.='					<option selected="selected" value="1">Active</option>
																		<option value="0">Inactive</option>
											';
									}else{
											$html.='					<option value="1">Active</option>
																		<option selected="selected" value="0">Inactive</option>
											';
									}

									$html .='	                    </select>
										                    	</div>
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
														<a href="'.$myPath.'struct/user/user-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
	                		$html.= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='user-main.php';
									</script>";
	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='user-main.php';
									</script>";
	            	}
	            } else {
	                header('Location: '.$myPath.'index.php?error=1');
	            }
	        }
	    } else { 
	        header('Location: '.$myPath.'index.php?error=1');
		}


	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>