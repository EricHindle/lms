<?php
$myPath = '../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/mail-util.php';

sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        header('Location: ' . $myPath . 'index.php?error=1');
    } else {
        if (isset($_POST['filename'])) {
            $filename = $_POST['filename'];

            $html = "";

            $strJsonFileContents = file_get_contents($filename);
            $array = json_decode($strJsonFileContents, true);

            $email = $array['toAddress'];
            $name = $array['toName'];
            $fromemail = $array['fromAddress'];
            $fromname = $array['fromName'];
            $bcc = $array['bcc'];
            $subject = $array['subject'];
            $body = $array['body'];
            $sentOk = sendmail($email, $subject, $body, $name, $bcc, $fromemail, $fromname);
            $html = ' <!doctype html>
            <html>
            <head>
            
            <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
            <meta charset="UTF-8">
            
            <title>Email Template</title>
            
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
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-offset-4 col-md-4 col-sm-offset-1 col-sm-10">
			                    <h1 style="text-align:center; color:white;"><strong>Email Test</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

            $html .= '			       <div class="well col-md-offset-1 col-md-8 col-sm-offset-1 col-sm-10 textDark">
			                  <form class="form-group" role="form" name ="emailtest" method="post" action="' . $myPath . 'testing/sendemail.php">';
            $html .= $key;
            $html .= '					     <h3 class="text-center">Enter Email Details</h3>
			                     <br>
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="email"  id="email" placeholder="to email address"  value="' . $email . '" >
                                 </div>
                    
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="name"  id="name" placeholder="to name"  value="' . $name . '" >
                                 </div>
                    
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="fromemail"  id="fromemail" placeholder="from email address"  value="' . $fromemail . '" >
                                 </div>
                    
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="fromname"  id="fromname" placeholder="from name"  value="' . $fromname . '" >
                                 </div>
                    
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="bcc"  id="bcc" placeholder="bcc list"  value="' . $bcc . '" >
                                 </div>
                    
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="subject"  id="subject" placeholder="subject"  value="' . $subject . '" >
                                 </div>
                    
                                 <div class="form-group">
                                    <input type="text" class="form-control" name="body"  id="body" placeholder="body"  value="' . $body . '" >
                                 </div>';
            if ($sentOk) {
                $html .= 'Email sent OK';
            } else {
                $html .= 'Email failed';
            }
            
                    
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


        } else {
            $html .= "<script>
							alert('Missing values in POST');
							window.location.href='" . $myPath . "testing/emailtest.php';
						</script>";
        }
    }
} else {
    $html .= "<script>
							alert('Not a POST');
							window.location.href='" . $myPath . "testing/emailtest.php';
						</script>";
}

echo $html;

?>   