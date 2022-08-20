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
$currentPage = '';
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
 			    <meta charset="UTF-8">
			    <title>LML Configuration</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			    <script src="' . $myPath . 'js/jquery.js"></script>
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
                <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Settings</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Add Value</h3>
                        <form role="form" name ="addinfo" method="post" action="add-info.php">';
    $html .= $key;
    $html .= '              <div class="form-group" style="margin:12px">
                                <div>
		                            <input type="text" class="form-field" id="infoid" name="infoid" placeholder="Value name" />
                                </div>
    	                    	<div>
    		                        <input type="text" class="form-field" id="infovalue" name="infovalue" placeholder="Value" />
                                </div>
                                <div>
                                    <input type="checkbox" style="margin-left:20px;" name="infoenc" id="infoenc" value="true" >
                                    <label for="infoenc">&nbsp Encrypted value</label>
                                </div>
			                </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
					    </form>
			        </div>
                    <div class="box" style="padding:1em;margin:10px;">
                        <h3>Edit Value</h3>
                        <form role="form" name ="editinfo" method="post" action="edit-info.php">';
    $html .= $key;
    $html .= ' 
			                <div class="form-group" style="margin:12px">
			                     <select class="form-dropdown" id="infoid" name="infoid">';
    foreach ($infofetch as $myinfo) {
        $html .= '                          <option value="' . $myinfo['lms_info_id'] . '">' . $myinfo['lms_info_id'] . '</option>';
    }
    $html .= '	                     </select>
                            </div>
                            <div class="form-group" style="margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
                        </form>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;text-align:left">
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
        $infovalue  = $rs['lms_info_value'];
        if ($rs['lms_info_enc'] == 1) {
            $infovalue = '[encrypted]';
        }
        $html .= '
        					<tr>
        						<td>' . $rs['lms_info_id'] . '</td>
        						<td>' . $infovalue . '</td>
        					</tr>';
    }
    $html .= '
						 </tbody>
                    </table>
				</div>
            </div>
		</body>
	</html>
		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
