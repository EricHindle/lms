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
            if (isset($_POST['user'])) {
                $userid = sanitize_int($_POST['user']);
                if ($userid) {

                    $html = "";
                    $usersql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_player_mobile, lms_access, lms_active FROM lms_player WHERE lms_player_id = :id";
                    $userquery = $mypdo->prepare($usersql);
                    $userquery->execute(array(
                        ':id' => $userid
                    ));
                    $usercount = $userquery->rowCount();

                    if ($usercount > 0) {
                        $key = $formKey->outputKey();
                        $userfetch = $userquery->fetch(PDO::FETCH_ASSOC);
                        $forename = $userfetch['lms_player_forename'];
                        $surname = $userfetch['lms_player_surname'];
                        $email = $userfetch['lms_player_email'];
                        $forename = decrypt($forename);
                        $surname = decrypt($surname);
                        $email = decrypt($email);
                        $mobile = '';
                        if (strlen($userfetch['lms_player_mobile']) > 0){
                            $mobile = decrypt($userfetch['lms_player_mobile']);
                        }
                        $isadmin = ($userfetch['lms_access'] == '999' ? 'checked' : '');
                        $isadmin = ($userfetch['lms_access'] == '901' ? 'checked' : '');
                        $isactive = ($userfetch['lms_active'] == '1' ? 'checked' : '');
                        echo '
							<!doctype html>';

                        $html .= '                <html>
                        <head>
                        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                        <meta charset="UTF-8">
                        
                        <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
                        <script src="' . $myPath . 'js/jquery.js"></script>
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        
                        <title>Last Man Live - Edit Player</title>
                        </head>
                        <body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
                        <div class="page-container" style="align-items: baseline;">
                        <div class="box" style="margin: 2em;">
                        <form role="form" name="details" method="post" action="process-edit-player.php" class="form-horizontal">';
                        $html .= $key;
                        $html .= '
                            <h2>Account Details</h2>
                            <div class="form-group" style="text-align:left">
                                <label for="fname" class="form-text">Forename:</label><br>
                                <input type="text"  class="form-field" id="fname" name="fname" value="' . $forename . '">
                            </div>
                            <div class="form-group" style="text-align:left">
                                <label for="sname" class="form-text">Surname:</label><br>
                                <input type="text"  class="form-field" id="sname" name="sname" value="' . $surname . '">
                            </div>
                            <div class="form-group" style="text-align:left">
                                <label for="mobile" class="form-text">Phone number:</label><br>
                                <input type="text"  class="form-field" id="mobile" name="mobile" value="' . $mobile . '">
                            </div>
                            <div class="form-group" style="text-align:left">
                                <label for="email" class="form-text">Email:</label><br>
                                <input type="text"  class="form-field" name="email"  id="email" value="' . $email . '">
                            </div>
                            <div class="form-group" style="text-align:left">
                                <label  for="screenname" class="form-text">Screen name:</label><br>
                                <input type="text"  class="form-field" id="screenname" name="screenname" value="' . $userfetch['lms_player_screen_name'] . '">
                            </div>
                            <div class="form-group" style="text-align:left;margin-bottom:5px;">
                               <input type="checkbox" style="margin-left:20px;" name="isadmin" id= "isadmin" value="true" ' . $isadmin . ' > 
                               <label for="isadmin">&nbsp is Administrator</label>
                               <input type="checkbox" style="margin-left:20px;" name="isactive" id="isactive" value="true" ' . $isactive . ' >
                               <label for="isactive">&nbsp is Active</label>
                            </div>

                            <div class="form-group">
                                <input type= "hidden" name= "userid" value="' . $userid . '" />
                                <input id="submit" name="submit" type="submit" value="Save Changes" class="btn">
                            </div>
                        </form>
                    </div>';

                        $html .= '     </div>

                			    	</div>
                			    </section>
                			</body>
                		</html>  ';
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