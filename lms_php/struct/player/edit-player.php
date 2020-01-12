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
							$key = $formKey->outputKey();
							$userfetch=$userquery->fetch(PDO::FETCH_ASSOC);
							
							$isadmin = '';
							if($userfetch['lms_access'] == '999'){
							    $isadmin = 'checked';
							}
							
							
							echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Edit Player</title>
									    
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
			                    <h1><strong>Edit player</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

		$html .= '			<div class="well col-md-10 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-player.php">';
		$html .= $key;
		$html .= '					<div class="form-group">
										<label class="control-label col-sm-2" for="name">Name:</label>
										<div class="col-sm-2">
										 	<p class="form-control-static" name="name">'.$userfetch['lms_player_forename'].' '.$userfetch['lms_player_surname'].'</p>
										</div>
										<label class="control-label col-sm-2" for="scrname">Screen name:</label>
										<div class="col-sm-2">
										 	<p class="form-control-static" name="scrname">'.$userfetch['lms_player_screen_name'].'</p>
										</div>
										<label class="control-label col-sm-2" for="oemail">Email:</label>
										<div class="col-sm-2">
										 	<p class="form-control-static" name="oemail">'.$userfetch['lms_player_email'].'</p>
										</div>
									</div>
                                    <div class="form-group">
                                           <label for="email">Email address:</label>
                                           <input type="text" class="form-control" name="email"  id="email" value="'.$userfetch['lms_player_email'].'">
                                           <label for="fname">Forename:</label>
					                       <input type="text" class="form-control" id="fname" name="fname" value="'.$userfetch['lms_player_forename'].'">
                                           <label for="sname">Surname:</label>
					                       <input type="text" class="form-control" id="sname" name="sname" value="'.$userfetch['lms_player_surname'].'">
                                           <label for="screenname">Screen name:</label>
					                       <input type="text" class="form-control" id="screenname" name="screenname" value="'.$userfetch['lms_player_screen_name'].'"><br>
                                           <input type="checkbox" name="isadmin" value="true" '.$isadmin.' > Administrator
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
								<a href="'.$myPath.'struct/player/player-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
										window.location.href='player-main.php';
									</script>";
	                	}
	                	
						echo $html;
			            
	                } else {
	                	echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='player-main.php';
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