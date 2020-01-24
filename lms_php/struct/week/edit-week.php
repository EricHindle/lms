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
	            if (isset($_POST['weekid']))
	            {
	                $gameid = $_POST['weekid'];
	                if($gameid)
	                {

	                	$html="";
	                	$weeksql = "SELECT * FROM lms_week WHERE lms_week_no = :id";
	                	$weekquery = $mypdo->prepare($weeksql);
	                	$weekquery->execute(array(':id' => $gameid));
	                	$weekcount = $weekquery->rowCount();

	                	if( $weekcount>0){
							$key = $formKey->outputKey();
							$remainingweeks=$weekquery->fetch(PDO::FETCH_ASSOC);
							echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Edit Pick Period</title>
									    
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
									                    <h1><strong>Edit Pick Period</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

								$html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-week.php">';
								$html .= $key;
								$html .= '					<h3 class="text-center">Period Dates</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="col-sm-2" for="season">Season:</label>
																<div class="col-sm-2">
																 	<p class="form-control-static" name="season" id="season">'.$remainingweeks['lms_year'].'</p>
																</div>
																<label class="col-sm-2" for="period">Period:</label>
																<div class="col-sm-2">
																 	<p class="form-control-static" name="period" id="period">'.$remainingweeks['lms_week'].'</p>
																</div>

															</div>

										                    <div class="form-group">
										                    	
                                                               <label for="startdate">New start date:</label>
                    					                       <input type="text" class="form-control" id="startdate" name="startdate" value="'.date_format(date_create($remainingweeks['lms_week_start']),'Y-m-d').'" placeholder="yyyy-mm-dd"><br>
                                                               <label for="deadline">New deadline:</label>
                    					                       <input type="text" class="form-control" id="deadline" name="deadline" value="'.date_format(date_create($remainingweeks['lms_week_deadline']),'Y-m-d').'" placeholder="yyyy-mm-dd"><br>
                                                               <label for="enddate">New end date:</label>
                    					                       <input type="text" class="form-control" id="enddate" name="enddate" value="'.date_format(date_create($remainingweeks['lms_week_end']),'Y-m-d').'" placeholder="yyyy-mm-dd"><br>

															   <input type= "hidden" name= "id" value="'.$gameid.'" />
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
														<a href="'.$myPath.'struct/week/week-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
										window.location.href='week-main.php';
									</script>";
	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='week-main.php';
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