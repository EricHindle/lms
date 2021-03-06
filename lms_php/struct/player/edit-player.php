<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['user'])) {
                $gameid = sanitize_int($_POST['user']);
                if ($gameid) {

                    $html = "";
                    $usersql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player WHERE lms_player_id = :id";
                    $userquery = $mypdo->prepare($usersql);
                    $userquery->execute(array(
                        ':id' => $gameid
                    ));
                    $usercount = $userquery->rowCount();

                    if ($usercount > 0) {
                        $key = $formKey->outputKey();
                        $userfetch = $userquery->fetch(PDO::FETCH_ASSOC);
                        $isadmin = ($userfetch['lms_access'] == '999' ? 'checked' : '');
                        $isactive = ($userfetch['lms_active'] == '1' ? 'checked' : '');
                        echo '
							<!doctype html>
							<html>
								<head>
									
								    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
								    <meta charset="UTF-8">
								    
								    <title>Edit Player</title>
								    
								    <meta name="viewport" content="width=device-width, initial-scale=1">
								    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
								    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
								    <script src="' . $myPath . 'js/jquery.js"></script>
								    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
								</head>

								<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
                    				<section id="homeSection">
                    			    <br><br>
                    			        <div class="container">
                    			        	<div class="row">
                    			                <div class="col-md-7 col-sm-7 col-xs-7">
                    			                    <h1><strong>Edit player</strong></h1>
                    			                    <br>
                    			                </div>
                    							<div class="col-md-1">
                    								<a href="' . $myPath . 'struct/player/player-main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
                    							</div>
                    
                    			      		</div>
                    			        	<div class = "row">';

                        $html .= '			   <div class="well col-md-8 col-sm-10 col-xs-offset-1 col-xs-10 textDark">
			                	                    <form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-player.php">';
                        $html .= $key;
                        $html .= '					
                                                    <div class="row">
                										<label class="control-label col-md-2  col-sm-2" for="name">Name:</label>
                										<div class="col-md-3 col-sm-3">
                										 	<p class="form-control-static" name="name">' . $userfetch['lms_player_forename'] . ' ' . $userfetch['lms_player_surname'] . '</p>
                										</div>
                										<label class="control-label col-md-3 col-sm-3" for="scrname">Screen name:</label>
                										<div class="col-md-3 col-sm-4">
                										 	<p class="form-control-static" name="scrname">' . $userfetch['lms_player_screen_name'] . '</p>
                										</div>
                                                    </div>
                                                    <div class="row">
                										<label class="control-label col-md-2 col-sm-2" for="oemail">Email:</label>
                										<div class="col-md-4 col-sm-4">
                										 	<p class="form-control-static" name="oemail">' . $userfetch['lms_player_email'] . '</p>
                										</div>
                                                        <div class="col-md-2 col-sm-2">
                                                        </div>
                									</div>
                                                    <div style="margin-top:16px">
                                                           <label for="email">Email address:</label>
                                                           <input type="text" class="form-control" name="email"  id="email" value="' . $userfetch['lms_player_email'] . '">
                                                           <label for="fname">Forename:</label>
                					                       <input type="text" class="form-control" id="fname" name="fname" value="' . $userfetch['lms_player_forename'] . '">
                                                           <label for="sname">Surname:</label>
                					                       <input type="text" class="form-control" id="sname" name="sname" value="' . $userfetch['lms_player_surname'] . '">
                                                           <label for="screenname">Screen name:</label>
                					                       <input type="text" class="form-control" id="screenname" name="screenname" value="' . $userfetch['lms_player_screen_name'] . '"><br>
                                                           <input type="checkbox" style="margin-left:20px;" name="isadmin" id= "isadmin" value="true" ' . $isadmin . ' > 
                                                           <label for="isadmin">&nbsp is Administrator</label>
                                                           <input type="checkbox" style="margin-left:20px;" name="isactive" id="isactive" value="true" ' . $isactive . ' >
                                                           <label for="isactive">&nbsp is Active</label>
                                                           <input type= "hidden" name= "id" value="' . $gameid . '" />
                                                     </div>
                									 <div>
                				                    	<br>
                				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                				                     </div>
                								</form>
                							</div>
                				        </div>
                				        <div>
                							<br>
                							<div class="col-xs-6">
                								<a href="' . $myPath . 'struct/player/player-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
                								<br>
                							</div>
                						</div>
                			    	</div>
                			    </section>
                			</body>
                		</html>  ';
                    } else {
                        $html .= "<script>
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
                header('Location: ' . $myPath . 'index.php?error=1');
            }
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>