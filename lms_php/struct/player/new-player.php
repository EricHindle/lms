<?php
	$myPath='../../';
	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath .'includes/formkey.class.php';
	sec_session_start(); 
		$formKey = new formKey();
		$key = $formKey->outputKey();

		$html="";

		echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Player Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="'.$myPath.'css/bootstrap.min.css">
			    <link rel="stylesheet" href="'.$myPath.'css/retlogin.css">
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
		$html.= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10">
			                    <h1 style="text-align:center; color:white;"><strong>Create an Account</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

		$html .= '			<div class="well col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10 textDark">
			                	<form class="form-group" role="form" name ="addplayer" method="post" action="'.$mypath.'add-new-player.php">';
		$html .= $key;
		$html .= '					<h3 class="text-center">Enter Your Details</h3>
			                    	<br>

                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                            <i class="glyphicon glyphicon-envelope"></i>
                                            <input type="text" class="form-control" name="email"  id="email" placeholder="email address">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                            <i class="glyphicon glyphicon-lock"></i>
                                            <input type="text" class="form-control" name="password"  id="password" placeholder="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                           <i class="glyphicon glyphicon-user"></i>
					                       <input type="text" class="form-control" id="fname" name="fname" placeholder="forename" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                           <i class="glyphicon glyphicon-user"></i>
					                       <input type="text" class="form-control" id="sname" name="sname" placeholder="surname" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                           <i class="glyphicon glyphicon-modal-window"></i>
					                       <input type="text" class="form-control" id="screenname" name="screenname" placeholder="screen name" />
					                    <input type="hidden" name="isadmin" value="false">';
		$html.='	                 
				                       </div>
                                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                                        <a href="'.$myPath.'index.php" class="btn btn-primary btn-small" role="button" style="float:right">Back</a>
				                    </div>
				                </form>
				            </div>
			            ';

	
		$html.= '	      		
			    	</div>
			    </section>
			</body>
		</html>

		';
		echo $html;

?>
