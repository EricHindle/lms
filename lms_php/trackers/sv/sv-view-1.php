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
					                		<form class="form-horizontal" role="form" id = "veform" name ="veform" method="post" action="sv-proc-s1.php" onSubmit="return countNA()">
					                			<input type="hidden" name="id" value="'.$survey_id.'"></input>
					                			<input type="hidden" id ="loclat" name="loclat" value="Location information is unavailable">
					                			<input type="hidden" id ="loclon" name="loclon" value="Location information is unavailable">
					';
					$html.=	$formKey->outputKey();
					$html.= '
												<div class="panel panel-primary">
													<div class="panel-heading">Exterior<small class = "pull-right">2 minutes</small></div>
												</div>
												<hr>
					
												<h4>Q1: Are the windows, including the front door and entrance area clean?</h4>

												<div class = "row">
													<div class="col-xs-10">
							
													<div class="input-group">
								    					<div class="scoreBtn btn-group">
								    						<a class="btn btn-primary  notActive" data-toggle="s1" data-title="0">0</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s1" data-title="1">1</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s1" data-title="2">2</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s1" data-title="3">3</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s1" data-title="4">4</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s1" data-title="5">5</a>
								    						<a class="btn btn-primary  active" data-toggle="s1" data-title="NA">N/A</a>
								    					</div>
								    					<input type="hidden" name="q1radio" class="s1" value="NA">
									    			</div>
									    		</div>
				    				
								    				<div class="col-xs-2">
								    					<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" data-info="Check that the area is free of:<br>•     weeds<br>•     litter<br>•     cigarette butts<br>Are Interserve cleaning the windows regularly?">Info</button>
								    				</div>
						    	
								    			</div>
				                                <br>
				                                <hr>
							
												<h4>Q2: Is the shop displaying the correct marketing?</h4>

												<div class = "row">
													<div class="col-xs-10">
							
														<div class="input-group">
									    					<div class="radioBtn btn-group">
									    						<a class="btn btn-primary  notActive" data-toggle="q2" data-title="Yes">YES</a>
									    						<a class="btn btn-primary  notActive" data-toggle="q2" data-title="No">NO</a>
									    						<a class="btn btn-primary  active" data-toggle="q2" data-title="NA">N/A</a>
									    					</div>
									    					<input type="hidden" name="q2radio" class="q2" value="NA">
										    			</div>
										    		</div>
									
								    				<div class="col-xs-2">
								    					<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" data-info="Please check ALBOS for the latest marketing display.">Info</button>
								    				</div>
						    	
								    			</div>
				                                <br>
				                                <hr>
							
												<h4>Q3: Is the Responsible Gambling window cling on display?</h4>

												<div class = "row">
													<div class="col-xs-10">
							
														<div class="input-group">
									    					<div class="radioBtn btn-group">
									    						<a class="btn btn-primary  notActive" data-toggle="q3" data-title="Yes">YES</a>
									    						<a class="btn btn-primary  notActive" data-toggle="q3" data-title="No">NO</a>
									    						<a class="btn btn-primary  active" data-toggle="q3" data-title="NA">N/A</a>
									    					</div>
									    					<input type="hidden" name="q3radio" class="q3" value="NA">
										    			</div>
										    		</div>
									
								    				<div class="col-xs-2">
								    					<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" data-info="Is the window cling in good condition?<br>If on display but in a poor condition please ask the Duty Manager to order a new one.">Info</button>
								    				</div>
						    	
								    			</div>
				                                <br>
				                                <hr>
							        
												<div class="form-group">
							                        <div class="col-xs-12">
							                        	<h4>Comments: <small>(<span id="remain">512</span> characters)</small></h4>
							                        	<textarea name="comments1" id="comments1" data-id ="1" rows="6" class="form-control" placeholder="Type your comments here"  ></textarea>
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