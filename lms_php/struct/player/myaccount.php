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
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            $userid = $_SESSION['user_id'];
            $username = $_SESSION['username'];
            if ($userid) {

                $html = "";
                $usersql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player WHERE lms_player_id = :id";
                $userquery = $mypdo->prepare($usersql);
                $userquery->execute(array(
                    ':id' => $userid
                ));
                $usercount = $userquery->rowCount();

                if ($usercount > 0) {
                    $key = $formKey->outputKey();
                    $userfetch = $userquery->fetch(PDO::FETCH_ASSOC);

                    echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
									    <meta charset="UTF-8">
									    
									    <title>Edit my details</title>
									    
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
                        			                <div class="col-md-8 col-sm-10 col-xs-8">
                        			                    <h1><strong>My Account</strong></h1>
                        			                    <br>
                        			                </div>
                        							<div class="col-md-1">
                        								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:35px" role="button">Back</a>
                        							</div>
                        			      		</div>
                        			        	<div class = "row">
                                                    <div class="col-md-12">';

                    $html .= '	                     <div class="well col-sm-offset-0 col-md-offset-0 col-md-4 col-sm-5 col-xs-offset-1 col-xs-8 textDark">
			                	                            <form role="form" name="details" method="post" action="process-myaccount.php" class="form-group">';
                    $html .= $key;
                    $html .= '	                         <h3 class="text-center">Change my details</h3>
                                                                <div class="form-group">
                                                                       <label for="email">Email address:</label>
                                                                       <input type="text" class="form-control" name="email"  id="email" value="' . $userfetch['lms_player_email'] . '">
                            									</div>
                                                                <div class="form-group">
                                                                       <label for="fname">Forename:</label>
                            					                       <input type="text" class="form-control" id="fname" name="fname" value="' . $userfetch['lms_player_forename'] . '">
                            									</div>
                                                                <div class="form-group">
                                                                       <label for="sname">Surname:</label>
                            					                       <input type="text" class="form-control" id="sname" name="sname" value="' . $userfetch['lms_player_surname'] . '">
                            									</div>
                                                                <div class="form-group">
                                                                       <label  for="screenname">Screen name:</label>
                                                                        <input type="text" class="form-control" id="screenname" name="screenname" value="' . $userfetch['lms_player_screen_name'] . '">
                                                                 </div>
                            									 <div class="form-group">
                                                                    <input type= "hidden" name= "userid" value="' . $userid . '" />
                            				                    	<br>
                            				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                            				                     </div>
                            								</form>
                            							</div>';

                    $html .= '			                               <div class="well col-md-4 col-md-offset-1 col-sm-offset-1 col-sm-5 col-xs-offset-1 col-xs-8 textDark">
			                	                            <form role="form" name ="password" method="post" action="change-my-password.php">';
                    $html .= $key;
                    $html .= '					                                 <h3 class="text-center">Change Password</h3>
                            									<h5>Password must contain at least 8 characters, including UPPERCASE, lowercase and numbers.</h5>
                                                                <br>
                                                                <div class="form-group">
                                                                    <label for="pwd1">New password:</label>
                                                                    <input name="pwd1" id="pwd1" class="form-control" type="password">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="pwd1">Confirm password:</label>
                                                                    <input id="pwd2" name="pwd2" class="form-control" title="Please enter the same Password as above." type="password">
                                                                </div>
                            				                    <div class="form-group">
                                                                    <input type= "hidden" name= "username" value="' . $username . '" />
                                                                    <br>
                            				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                            				                    </div>
                            				                </form>
                            				            </div>
                            			             </div>         ';
                    $html .= '					                     <div class="row">
                            							<br>
                            							<div class="col-xs-6">
                            								<a href="' . $myPath . 'menus/home.php" class="btn btn-primary" role="button">Back</a>
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
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>