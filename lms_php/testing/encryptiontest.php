<?php
$myPath = '../';

require_once $myPath . 'includes/functions.php';
require_once $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/info/info_functions.php';
sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();
$plaintext = '';
$enctext = '';
$encrypt = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $plaintext = (isset($_POST['plaintext']) ? $_POST['plaintext'] : '');
    $enctext = (isset($_POST['enctext']) ? $_POST['enctext'] : '');
    $encrypt = (isset($_POST['isencrypt']) ? true : false);


    if ($encrypt) {
        $enctext = combobulate($plaintext, "e");
    } else {
        $plaintext = combobulate($enctext, "d");
    }
}

$isencrypt = ($encrypt ? 'checked' : '');

$html = "";

$html = ' <!doctype html>
            <html>
            <head>
            
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta charset="UTF-8">
            
            <title>Encryption</title>
            
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
            <link rel="stylesheet" href="' . $myPath . 'css/retlogin.css">
            <script src="' . $myPath . 'js/jquery.js"></script>
            <script src="' . $myPath . 'js/bootstrap.min.js"></script>

			</head>
                    
			<body>';
$html .= '
				<section id="homeSection">
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10">
			                    <h1 style="text-align:center; color:white;"><strong>Encryption Test</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

$html .= '			       <div class="well col-md-offset-1 col-md-8 col-sm-offset-1 col-sm-10 textDark">
			                  <form class="form-group" role="form" name ="emailtest" method="post" action="' . $myPath . 'testing/encryptiontest.php">';
$html .= $key;
$html .= '					 
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="plaintext"  id="plaintext" placeholder="plain text"  value="' . $plaintext . '" >
                                 </div>
                   
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="enctext"  id="enctext" placeholder="encrypted text"  value="' . $enctext . '" >
                                 </div>
                                <div class="form-group">
                                       <input type="checkbox" style="margin-left:20px;" name="isencrypt" id="isencrypt" value="true" ' . $isencrypt . ' >
                                       <label for="issendemail">&nbsp encrypt</label>
                                </div>
';

$html .= '                 <div class="form-group">
				                    <br>
				                    <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
                                    <a href="' . $myPath . 'menus/testmenu.php" class="btn btn-primary btn-small" role="button" style="float:right">Back</a>
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