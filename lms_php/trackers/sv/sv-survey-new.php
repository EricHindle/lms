<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
if (login_check($mypdo) == true) {
    if (isset($_SESSION['svid'], $_SESSION['svsec'])) {
        $survey_id = sanitize_int($_SESSION['svid']);
        $current_section = sanitize_int($_SESSION['svsec']);
        unset($_SESSION['svid']);
        // unset($_SESSION['svsec']);
        if ($survey_id && $current_section) {
            $chk_exists_sql = "SELECT id, shop, cem FROM svchks WHERE id = :id LIMIT 1";
            $chk_exists_query = $mypdo->prepare($chk_exists_sql);
            $chk_exists_query->execute(array(
                ':id' => $survey_id
            ));
            $chk_exists = $chk_exists_query->rowCount();
            if ($chk_exists > 0) {
                $chk_exists_fetch = $chk_exists_query->fetch(PDO::FETCH_ASSOC);
                $shop = $chk_exists_fetch['shop'];
                $cem = $chk_exists_fetch['cem'];
                $formKey = new formKey();
                $questions_sql = "SELECT q.text as question, q.info as info, q.section as section, q.questions_id as id, q.type as type from questions q where q.active_question = 1 and q.section = :section";
                $questions_query = $mypdo->prepare($questions_sql);
                $questions_query->execute(array(
                    ':section' => $current_section
                ));
                $questions_fetch = $questions_query->fetchAll(PDO::FETCH_ASSOC);
                $sections_sql = "SELECT * from sections where sections_id = :section LIMIT 1";
                $sections_query = $mypdo->prepare($sections_sql);
                $sections_query->execute(array(
                    ':section' => $current_section
                ));
                $sections_fetch = $sections_query->fetch(PDO::FETCH_ASSOC);
                $html = '';

                echo '
					<!doctype html>
					<html>
						<head>
						    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
						    <meta charset="UTF-8">
						    
						    <title>Smart Visit</title>
						    
						    <meta name="viewport" content="width=device-width, initial-scale=1">
						    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
						    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
						    <script src="' . $myPath . 'js/jquery.js"></script>
						    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
						    <script src="' . $myPath . 'js/surveys.js"></script>
					';
                if ($current_section < 2) {
                    echo '
							<script src="' . $myPath . 'js/getloc.js"></script>
						';
                }

                echo '				</head>

						<body>';

                include $myPath . 'globNAV.php';

                $html .= '
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
					                			<h2 class="text-center">' . $shop . '</h2>
												<h6 class ="text-center small">' . $cem . '</h6>
												<h6 class ="text-center small" id="myLocation"></h6>
					                		</div>	
					                		<form class="form-horizontal" role="form" id = "veform" name ="veform" method="post" action="sv-proc-sec.php" onSubmit="return countNA()">
					                			<input type="hidden" name="id" value="' . $survey_id . '"></input>
					                			<input type="hidden" id ="loclat" name="loclat" value="Location information is unavailable">
					                			<input type="hidden" id ="loclon" name="loclon" value="Location information is unavailable">

	    			';
                $html .= $formKey->outputKey();
                $html .= '	
												<div class="panel panel-primary">
													<div class="panel-heading">' . $sections_fetch['section_name'] . '<small class = "pull-right">' . $sections_fetch['section_times'] . ' mins</small></div>
												</div>
												<hr>
					';
                // foreach ($json['questions']as $value) {
                
                $qno = 0;
                
                foreach ($questions_fetch as $value) {
                    $qno += 1;
                    // if ($value['section']==$current_section) {
                    $html .= '
												<h4>Q'. $current_section . '.' . $qno . ': ' . $value['question'] . '</h4>

												<div class = "row">
													<div class="col-xs-10">
							';
                    switch ($value['type']) {
                        case 'yn':
                            $html .= '
														<div class="input-group">
									    					<div class="radioBtn btn-group">
									    						<a class="btn btn-primary  notActive" data-toggle="q' . $value['id'] . '" data-title="Yes">YES</a>
									    						<a class="btn btn-primary  notActive" data-toggle="q' . $value['id'] . '" data-title="No">NO</a>
									    						<a class="btn btn-primary  active" data-toggle="q' . $value['id'] . '" data-title="NA">N/A</a>
									    					</div>
									    					<input type="hidden" name="q' . $value['id'] . 'radio" class="q' . $value['id'] . '" value="NA">
										    			</div>
										    		</div>
									';
                            break;
                        case 'num':
                            $html .= '
													<div class="input-group">
								    					<div class="scoreBtn btn-group">
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="0">0</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="1">1</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="2">2</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="3">3</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="4">4</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="5">5</a>
								    						<a class="btn btn-primary  active" data-toggle="s' . $value['id'] . '" data-title="NA">N/A</a>
								    					</div>
								    					<input type="hidden" name="q' . $value['id'] . 'radio" class="s' . $value['id'] . '" value="NA">
									    			</div>
									    		</div>
				    				';
                            break;
                        case 'num2':
                            $html .= '
													<div class="input-group">
								    					<div class="scoreBtn btn-group">
								    						<a class="btn btn-primary  active" data-toggle="s' . $value['id'] . '" data-title="0">0</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="1">1</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="2">2</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="3">3</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="4">4</a>
								    						<a class="btn btn-primary  notActive" data-toggle="s' . $value['id'] . '" data-title="5">5</a>
								    					</div>
								    					<input type="hidden" name="q' . $value['id'] . 'radio" class="s' . $value['id'] . '" value="0">
									    			</div>
									    		</div>
				    				';
                            break;
                        case 'yn2':
                            $html .= '
														<div class="input-group">
									    					<div class="radioBtn btn-group">
									    						<a class="btn btn-primary  notActive" data-toggle="q' . $value['id'] . '" data-title="Yes">YES</a>
									    						<a class="btn btn-primary  active" data-toggle="q' . $value['id'] . '" data-title="No">NO</a>
									    					</div>
									    					<input type="hidden" name="q' . $value['id'] . 'radio" class="q' . $value['id'] . '" value="No">
										    			</div>
										    		</div>
									';
                            break;
                        case 'done':
                            $html .= '
														<div class="input-group">
									    					<div class="radioBtn btn-group">
									    						<a class="btn btn-primary  notActive" data-toggle="q' . $value['id'] . '" data-title="Done">Done</a>
									    						<a class="btn btn-primary  active" data-toggle="q' . $value['id'] . '" data-title="NA">N/A</a>
									    					</div>
									    					<input type="hidden" name="q' . $value['id'] . 'radio" class="q' . $value['id'] . '" value="NA">
										    			</div>
										    		</div>
									';
                            break;
                    }
                    $html .= '                          <div class="input-group">
                                                             <input type="hidden" name="qno' . $value['id'] . '" value=' . $qno . '>
                                                        </div>';
                    if ($value['info'] != "") {
                        $html .= '
								    				<div class="col-xs-2">
								    					<button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#myModal" data-info="' . nl2br($value['info']) . '">Info</button>
								    				</div>
						    	';
                    }
                    $html .= '
								    			</div>
			                                    <br>
			                                    <hr>
							';
                }
                // }
                $html .= '        
												<div class="form-group">
							                        <div class="col-xs-12">
							                        	<h4>Comments: <small>(<span id="remain">512</span> characters)</small></h4>
							                        	<textarea spellcheck="true" name="comments' . $current_section . '" id="comments' . $current_section . '" data-id ="' . $current_section . '" rows="6" class="form-control" placeholder="Type your comments here"  ></textarea>
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
					';
                $html .= '
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
                bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not found in DB");
            }
        } else {
            bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Suspicious data");
        }
    } else {
        bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Session data not set");
    }
} else {
    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Couldn't verify user");
}
?>