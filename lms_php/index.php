<?php

require 'includes/db_connect.php';
require 'includes/functions.php';
require 'includes/formkey.class.php';
require 'includes/index-functions.php';

sec_session_start();
$formKey = new formKey();

if (login_check($mypdo) == true) {
    header('Location: logout.php');
} else {

    $_SESSION['currentweek'] = get_global_value('currweek');
    $_SESSION['currentseason'] = get_global_value('currseason');
    $_SESSION['selectweek'] = get_global_value('selectweek');
    $_SESSION['matchweek'] = $_SESSION['currentseason'] . $_SESSION['currentweek'];
    $_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $_SESSION['selectweek'];
    $_SESSION['selperiod']=   $_SESSION['selectweek'] . '/' .$_SESSION['currentseason'] ;
    $_SESSION['deadline'] = get_current_deadline_date($_SESSION['selectweekkey']);   
    
    $html = '
    <!DOCTYPE html>
    <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="css/login.css" type="text/css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
        <title>Last Man Live - Login</title>
      </head>
      <body>
      <div class="container">
          <div class="box">
              <img src="img/LastmanLogo-650.png" alt="logo" class="logo" />
              <form role="form" autocomplete="off" name="form1" method="post" action="process_login.php" class="form-group">
    ';
    $html .= $formKey->outputKey();
    $html .= '
    <input type="text" class="form-field" name="username"  id="username" placeholder="email">
            
    <input type="password" class="form-field" name="password" id="password" placeholder="password" autocomplete="off">
    
    <button class="btn" type="submit">Sign in</button>
    </form>

    <div class="dark-text">
        <a href="struct/player/new-password.php">Forgotten Password</a>
     </div>
</div>
                                ';
    if (isset($_GET['error'])) {
        $html .= '<div class="error-message">Your email and password combination is incorrect.</div>';
    }
    $html .= '            
    <div class="light-text">
    <a href="struct/player/new-player.php" role="button">Create Account</a>
</div>
</div>
</body>
</html>
    ';
    echo $html;
}
?>
