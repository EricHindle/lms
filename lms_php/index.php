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
        <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <title>Last Man Live</title>
                <link rel="stylesheet" href="css/bootstrap.min.css" />
                <link rel="stylesheet" href="css/retlogin.css">
            </head>
            <body>
                <div class="container-fluid">
                    <div class="row">
                        <section id="login">
                            <div class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6  col-xs-offset-1 col-xs-10">
                            <br><br>
                                <img class="img-responsive center-block" src="img/logo_big.png">
                                <form role="form" autocomplete="off" name="form1" method="post" action="process_login.php" class="form-group">
    ';
    $html .= $formKey->outputKey();
    $html .= '
                                    <h1 class="text-center">Welcome</h1>
                                    <br>
                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                            <i class="glyphicon glyphicon-envelope"></i>
                                            <input type="text" class="form-control input-lg" name="username"  id="username" placeholder="email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="inner-addon left-addon">
                                            <i class="glyphicon glyphicon-lock"></i>
                                            <input name="password" type="password" class="form-control input-lg" name="password" id="password" placeholder="password" autocomplete="off">
                                        </div>
<a href="struct/player/new-password.php" class="" style="float:right">Forgotten Password</a>
                                        <br>
                                    </div>
                                        <button class="btn btn-primary btn-md" type="submit">Sign in <span class="glyphicon glyphicon-log-in"></button>

                                        <a href="struct/player/new-player.php" class="btn btn-primary btn-small" role="button" style="float:right">Create Account <span class="glyphicon glyphicon-globe"></a>

                                </form>


                                ';
    if (isset($_GET['error'])) {
        $html .= '<div class="alert alert-danger">There has been a problem</div>';
    }
    $html .= '            </div>
                        </section>
                    </div>
                </div>
            </body>
        </html>
    ';
    echo $html;
}
?>
