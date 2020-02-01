<?php
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
            if (isset($_POST['infoid'])) {
                $gameid = $_POST['infoid'];
                if ($gameid) {

                    $html = "";
                    $infosql = "SELECT lms_info_id, lms_info_value FROM lms_info WHERE lms_info_id = :id";
                    $infoquery = $mypdo->prepare($infosql);
                    $infoquery->execute(array(
                        ':id' => $gameid
                    ));
                    $infocount = $infoquery->rowCount();

                    if ($infocount > 0) {
                        $key = $formKey->outputKey();
                        $infofetch = $infoquery->fetch(PDO::FETCH_ASSOC);
                        $isactive = "";
                        if ($infofetch["lms_info_active"] == 1) {
                            $isactive = "checked";
                        }
                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Edit info</title>
									    
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
									                <div class="col-md-8">
									                    <h1><strong>Edit Configuration Value</strong></h1>
									                </div>
									      		</div>
									        	<div class = "row">';

                        $html .= '			<div class="well col-md-6 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-info.php">';
                        $html .= $key;
                        $html .= '					<h3 class="text-center">Edit value</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="col-sm-3" for="name">Value name:</label>
																<div class="col-sm-3">
																 	<p class="form-control-static" name="name">' . $infofetch['lms_info_id'] . '</p>
																</div>
															</div>

										                    <div class="form-group">
										                    	
                                                               <label for="infoname">New value:</label>
                    					                       <input type="text" class="form-control" id="infovalue" name="infovalue" value="' . $infofetch['lms_info_value'] . '"><br>
															   <input type= "hidden" name= "id" value="' . $gameid . '" />
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
														<a href="' . $myPath . 'struct/info/info-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
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