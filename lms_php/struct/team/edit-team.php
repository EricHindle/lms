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
	            if (isset($_POST['team']))
	            {
	                $id = sanitize_int($_POST['team']);
	                if($id)
	                {

	                	$html="";
	                	$teamsql = "SELECT lms_team_id, lms_team_name, lms_team_active FROM lms_team WHERE lms_team_id = :id";
	                	$teamquery = $mypdo->prepare($teamsql);
	                	$teamquery->execute(array(':id' => $id));
	                	$teamcount = $teamquery->rowCount();

	                	if( $teamcount>0){
							$key = $formKey->outputKey();
							$teamfetch=$teamquery->fetch(PDO::FETCH_ASSOC);
							$isactive = "";
							if($teamfetch["lms_team_active"] == 1) {
							    $isactive = "checked";
							}
							echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Edit Team</title>
									    
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
									                <div class="col-md-8">
									                    <h1><strong>Edit Team</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

								$html .= '			<div class="well col-md-6 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-team.php">';
								$html .= $key;
								$html .= '					<h3 class="text-center">Edit Team</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="control-label col-sm-2" for="name">Name:</label>
																<div class="col-sm-4">
																 	<p class="form-control-static" name="name">'.$teamfetch['lms_team_name'].'</p>
																</div>
															</div>

															<div class="form-group">
																<label class="control-label col-sm-2" for="oactive">Status:</label>
																<div class="col-sm-4">';
									if ($teamfetch['lms_team_active']==1) {
										$html.='					<p class="form-control-static" name="ooactive">Active</p>';
									}else{
										$html.='					<p class="form-control-static" name="ooactive">Inactive</p>';
									}

									$html.='					</div>
															</div>
										                    <div class="form-group">
										                    	
                                                               <label for="teamname">New name:</label>
                    					                       <input type="text" class="form-control" id="teamname" name="teamname" value="'.$teamfetch['lms_team_name'].'"><br>
                                                               <input type="checkbox" style="margin-left:20px;" name="isactive" id="isactive" value="true" '.$isactive.' >
                                                               <label for="isactive">&nbsp is Active</label>
															   <input type= "hidden" name= "id" value="'.$id.'" />
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
														<a href="'.$myPath.'struct/team/team-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
										window.location.href='team-main.php';
									</script>";
	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='team-main.php';
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