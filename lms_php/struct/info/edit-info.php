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
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access > 900) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['infoid'])) {
                $gameid = $_POST['infoid'];
                if ($gameid) {

                    $html = "";
                    $infosql = "SELECT lms_info_id, lms_info_value, lms_info_enc FROM lms_info WHERE lms_info_id = :id";
                    $infoquery = $mypdo->prepare($infosql);
                    $infoquery->execute(array(
                        ':id' => $gameid
                    ));
                    $infocount = $infoquery->rowCount();

                    if ($infocount > 0) {
                        $key = $formKey->outputKey();
                        $infofetch = $infoquery->fetch(PDO::FETCH_ASSOC);
                        echo '
		<!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Settings</title>
			    <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
			</head>

									<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '

									        <div class="container">
                    <div class="box" style="padding:1em;">
                        <h2>Edit Setting</h2>
                    </div>
                    <div class="box" style="padding:1em;margin:10px;">
                     	<form role="form" name ="edit" method="post" action="process-edit-info.php">';
                        $html .= $key;
                        $enc = "";
                           $infovalue = $infofetch['lms_info_value'];
                        if($infofetch['lms_info_enc']  == 1) {
                             $enc = "checked";
                               $infovalue = decrypt($infovalue);
                        }
                        $html .= '					  
               	            <div class="form-group" style="padding:25px;text-align:left;">
                                <div>
									<label class="form-text" style="display:inline-block;width:30%;text-align:left">Value name:</label>' . $infofetch['lms_info_id'] . '
								</div>
							</div>

		                    <div class="form-group"  style="padding-left:10px;text-align:left;">	                    	
                               <label class="form-text" style="display:inline-block;width:40%;text-align:left">New value:</label>
		                       <input type="text" class="form-field" id="infovalue" name="infovalue" value="' . $infovalue . '"><br>
                               <input type="checkbox" style="margin-left:20px;" name="infoenc" id="infoenc" value="true" ' . $enc . ' >
                               <label for="infoenc">&nbsp Encrypted value</label>
							   <input type= "hidden" name= "id" value="' . $gameid . '" />
		                    </div>
                            <div class="form-group" style="padding-top:25px;margin-left:16px;margin-right:16px">
					            <input id="submit" name="submit" type="submit" value="Submit" class="btn graybutton" style="padding:5px;width:50%;">
					        </div>
		                </form>
                            <div class="light-text">
					            <a href="' . $myPath . 'struct/info/info-main.php">Back</a>
					        </div>
		            </div>
		        </div>
	</body>
								</html>
									            ';
                    } else {
                        $html .= "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='info-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='info-main.php';
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