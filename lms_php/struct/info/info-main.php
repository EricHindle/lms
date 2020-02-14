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
if (login_check($mypdo) == true && $_SESSION['retaccess'] > 900) {
    $formKey = new formKey();
    $key = $formKey->outputKey();

    $infosql = "SELECT * FROM lms_info";
    $infoquery = $mypdo->prepare($infosql);
    $infoquery->execute();
    $infofetch = $infoquery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Configuration</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
			    <script>
		            $(function(){
		            $(\'#keywords\').tablesorter(); 
		            });
		        </script>
			</head>

			<body>';
    include $myPath . 'globNAV.php';
    $html .= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-11">
			                    <h1><strong>Info Admin</strong></h1>
			                </div>
							<div class="col-md-1">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-sm" style="margin-bottom:10px;margin-top:20px" role="button">Back</a>
							</div>
			      		</div>
			        	<div class = "row">';

    $html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="addinfo" method="post" action="add-info.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Add Value</h3>
				                    <div class="form-group">
				                    	<label for="infoid">Value Name:</label>
					                    <input type="text" class="form-control" id="infoid" name="infoid" placeholder="Value name" />
				                    	<label for="infovalue">Value:</label>
					                    <input type="text" class="form-control" id="infovalue" name="infovalue" placeholder="Value" />
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '			<div class="well col-md-3 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="editinfo" method="post" action="edit-info.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Edit Value</h3>
				                    <div class="form-group">
			                        	<label for="user">Choose value:</label>
			                            <select class="form-control" id="infoid" name="infoid">';
    foreach ($infofetch as $myinfo) {
        $html .= '<option value="' . $myinfo['lms_info_id'] . '">' . $myinfo['lms_info_id'] . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group">
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary btn-sm">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '		</div>
						<div class = "row">
				        	<div class="well col-md-7 col-md-offset-1 textDark">
				        		<h3>All values</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
										<th>Name</th>
										<th>Value</th>
									</tr>
									</thead>
									<tbody>
									';
    foreach ($infofetch as $rs) {
        $html .= '
									<tr>
										<td>' . $rs['lms_info_id'] . '</td>
										<td>' . $rs['lms_info_value'] . '</td>
									</tr>';
    }
    $html .= '
									</tbody>
								</table>
							</div>
						</div>

				        ';

    $html .= '	      		
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
