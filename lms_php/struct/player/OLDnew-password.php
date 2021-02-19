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
$key = $formKey->outputKey();

$html = "";

echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
			    <meta charset="UTF-8">
			    
			    <title>Player Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/retlogin.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
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
			</head>

			<body>';
$html .= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10">
			                    <h1 style="text-align:center; color:white;"><strong>Request A New Password</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';
$html .= '			<div class="well col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10 textDark">
			                	<form class="form-group" role="form" name ="addplayer" autocomplete="off" method="post" action="' . $myPath . 'struct/player/add-temp-password.php">';
$html .= $key;
$html .= '					<h3 class="text-center">Enter Your Email Address</h3>
			                    	<br>
                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                            <i class="glyphicon glyphicon-envelope"></i>
                                            <input type="text" class="form-control" name="email"  id="email" placeholder="email address"  value="" >
                                        </div>
                                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                                        <a href="' . $myPath . 'index.php" class="btn btn-primary btn-small" role="button" style="float:right">Back</a>
				                    </div>
				                </form>
				            </div>
			            ';
$html .= '	      		
			    	</div>
			    </section>
			</body>
		</html>

		';
echo $html;

?>
