<?php
$myPath = '../../';

require_once $myPath . 'includes/functions.php';
require_once $myPath . 'includes/formkey.class.php';
require_once $myPath . 'struct/email/email-functions.php';

sec_session_start();
$formKey = new formKey();
$key = $formKey->outputKey();
$devlevelneeded = 901;

if (login_check($mypdo) == true && $_SESSION['retaccess'] == $devlevelneeded) {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (! isset($_GET['form_key'])) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_GET['cmd'])) {
                $cmd = $_GET['filename'];

                $html = "";
                $output = null;
                $retval = null;
                exec($cmd, $output, $retval);

                $html .= "<script>
							alert('Job complete '" . $retval . " );
							window.location.href='" . $myPath . "menus/testmenu.php';
						</script>";


            } else {
                $html .= "<script>
							alert('Missing values in POST');
							window.location.href='" . $myPath . "menus/testmenu.php';
						</script>";
            }
        }
    } else {
        $html .= "<script>
							alert('Not a POST');
							window.location.href='" . $myPath . "menus/testmenu.php';
						</script>";
    }

    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>   