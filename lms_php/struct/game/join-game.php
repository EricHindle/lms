<?php
	$myPath='../../';

	require $myPath.'includes/db_connect.php';
    require $myPath.'includes/functions.php';
    require $myPath.'includes/formkey.class.php';

	sec_session_start();
	$formKey = new formKey();
	if(login_check($mypdo) == true) {
    	$html="";
		$key = $formKey->outputKey();
		$isactive = "";
		echo '
			<!doctype html>
			<html>
				<head>
					
				    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
				    <meta charset="UTF-8">
				    
				    <title>Join a Game</title>
				    
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
					                    <h1><strong>Join a Game</strong></h1>
					                </div>
					      		</div>
					        	<div class = "row">';

				$html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
					                	<form role="form" name ="edit" method="post" action="process-join-game.php">';
				$html .= $key;
				$html .= '					<h3 class="text-center">Game Code</h3>
					                    	<br>
						                    <div class="form-group " style="margin-left:24px;margin-right:24px">
    					                        <input type="text" class="form-control" id="gamecode" name="gamecode" placeholder="game code">
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
										<a href="'.$myPath.'struct/game/game-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
										<br>
									</div>
								</div>
					      		<br><br><br><br>
					    	</div>
					    </section>
					</body>
				</html>
					            ';
    	
		echo $html;
	} else { 
	        header('Location: '.$myPath.'index.php?error=1');
	}
?>