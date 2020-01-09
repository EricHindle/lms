<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
	require $myPath.'includes/functions.php';
	require $myPath.'includes/formkey.class.php';
	sec_session_start();
	if( login_check($mypdo) == true && isset($_SESSION['svid']) ) {
		$survey_id = sanitize_int($_SESSION['svid']);
		unset($_SESSION['svid']);

		if ($survey_id) {
			$chk_exists_sql = "SELECT id, shop, cem FROM svchks WHERE id = :id LIMIT 1";
			$chk_exists_query = $mypdo->prepare($chk_exists_sql);
			$chk_exists_query->execute(array(':id'=>$survey_id));
			$chk_exists_fetch = $chk_exists_query->fetch(PDO::FETCH_ASSOC);
			if ($chk_exists_fetch) {
				$shop = $chk_exists_fetch['shop'];
				$cem = $chk_exists_fetch['cem'];
				$formKey = new formKey();
				$html ='';
				echo '
					<!doctype html>
					<html>
						<head>
						    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
						    <meta charset="UTF-8">
						    
						    <title>Smart Visit</title>
						    
						    <meta name="viewport" content="width=device-width, initial-scale=1">
						    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
						    <link rel="stylesheet" href="'.$myPath.'css/rethome.css">
						    <script src="'.$myPath.'js/jquery.js"></script>
						    <script src="'.$myPath.'js/bootstrap.min.js"></script>
						    <script src="'.$myPath.'js/surveys.js"></script>
							<script src="'.$myPath.'js/getloc.js"></script>
						</head>
						<body>';

				include $myPath.'globNAV.php';
				$html.= '
					    	<br><br><br><br>
					        <div class="container">
					        	<div class="modal fade" id="myModal" role="dialog">
									<div class="modal-dialog modal-lg textDark">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title"></h4>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>
					        	<div class="row">
					                <section id="veform">
					                	<div class="well well-xs col-xs-12">
					                		<div class = "jumbotron">
					                			<h2 class="text-center">'.$shop.'</h2>
												<h6 class ="text-center small">'.$cem.'</h6>
												<h6 class ="text-center small" id="myLocation"></h6>
					                		</div>	
					                		<form class="form-horizontal" role="form" id = "veform" name ="veform" method="post" action="sv-proc-s2.php" onSubmit="return countNA()">
					                			<input type="hidden" name="id" value="'.$survey_id.'"></input>
					                			<input type="hidden" id ="loclat" name="loclat" value="">
					                			<input type="hidden" id ="loclon" name="loclon" value="">
					';
					$html.=	$formKey->outputKey();
					$html.= '	
												<div class="panel panel-primary">
													<div class="panel-heading">Entrance<small class = "pull-right">10 minutes</small></div>
												</div>
												<hr>
					
												<h4>Q4: Are all relevant notices displayed correctly?</h4>

												<div class = "row">
													<div class="col-xs-10">
							
														<div class="input-group">
									    					<div class="radioBtn btn-group">
									    						<a class="btn btn-primary  notActive" data-toggle="q4" data-title="Yes">YES</a>
									    						<a class="btn btn-primary  notActive" data-toggle="q4" data-title="No">NO</a>
									    						<a class="btn btn-primary  active" data-toggle="q4" data-title="NA">N/A</a>
									    					</div>
									    					<input type="hidden" name="q4radio" class="q4" value="NA">
										    			</div>
										    		</div>
									
								    				<div class="col-xs-2">
								    					<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" data-info="<b><u>Statutory Notices</u></b><br>These notices must be positioned adjacent to the front door so that they are clearly visible to everyone either entering or leaving the premises.<br><br>1) Slim A2 Security notice, which covers No Smoking, No Under 18’s, No Alcohol, No Helmets, Security Alarm, Time Locked Safe and CCTV<br>2) Responsible Gambling Dispenser and Leaflets<br><br><b><u>Information Notices</u></b><br>Information notices should be displayed in the least prominent non-selling area of the shop.<br><br>•     Slim A2 poster Excellence in Customer Service<br>•     Crime Stoppers Dispenser and Flyers<br>•     Good Neighbour Dispenser and Leaflets<br><br>Additional notices may be displayed if agreed with your Line Manager or Security.<br>">Info</button>
								    				</div>
						    	
								    			</div>
			                                    <br>
			                                    <hr>
							        
												<div class="form-group">
							                        <div class="col-xs-12">
							                        	<h4>Comments: <small>(<span id="remain">512</span> characters)</small></h4>
							                        	<textarea name="comments2" id="comments2" data-id ="2" rows="6" class="form-control" placeholder="Type your comments here"  ></textarea>
							                        </div>
							                    </div>
												<div class="form-group">
					                                <div class="col-sm-6">
					                                    <br><br>
					                                    <input id="submit" name="submit" type="submit" value="Save &amp continue" class="btn btn-success btn-lg">
					                                </div>
					                            </div>
											</form>
										</div>        	
					                </section>
					            </div>
					
									<div class="row">
										<br>
										<div class="col-sm-12">
											<a href="sv-main.php" name="exitBtn" id="exitBtn" onclick="return ruSure()" class="btn btn-primary btn-lg" role="button">Exit Survey</a>
											<br>
										</div>
									</div>
									<br><br>
						    </div>
						</body>
					</html>
				';
				echo $html;
			} else { 
	        	bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Data not found in DB");
			}
		} else { 
        	bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Suspicious data");
		}
	} else { 
        bootOut($myPath, $user=$_SESSION['fname'],basename($_SERVER['PHP_SELF']), "Couldn't verify user");
	}
?>