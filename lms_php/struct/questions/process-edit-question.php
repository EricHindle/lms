<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
$access = sanitize_int($_SESSION['retaccess']);
if (login_check($mypdo) == true && $access == 999) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            if (isset($_POST['id'], $_POST['question'], $_POST['info'], $_POST['type'], $_POST['section'])) {
                $id = sanitize_int($_POST['id']);
                $question = $_POST['question'];
                $info = $_POST['info'];
                $type = $_POST['type'];
                $section = $_POST['section'];
                $activeq = '';
                if (isset($_POST['activeq'])) {
                    $activeq = $_POST['activeq'];
                }
                if ($id && $question && $type && $section) {

                    $html = "";

                    date_default_timezone_set('Europe/London');
                    $phptime = time();
                    $myaccess = 0;
                    $mysqltime = date("Y-m-d H:i:s", $phptime);
                    $isactive = 0;
                    if ($activeq == 'active') {
                        $isactive = 1;
                    } 
                    $upsql = "UPDATE questions SET text = :question, info = :info, type = :type, section = :section, active_question = :active WHERE questions_id = :id";
                    $upquery = $mypdo->prepare($upsql);
                    $upquery->execute(array(
                        ':question' => $question,
                        ':info' => $info,
                        ':type' => $type,
                        ':section' => $section,
                        ':active' => $isactive,
                        ':id' => $id
                    ));
                    $upcount = $upquery->rowCount();
                    if ($upcount > 0) {
                        $html .= "<script>
												alert('Details updated successfully.');
												window.location.href='question-main.php';
											</script>";
                    } else {
                        $html .= "<script>
										alert('Record not changed.');
										window.location.href='question-main.php';
									</script>";
                    }

                    echo $html;
                } else {
                    echo "<script>
										alert('There was a problem. Please check details and try again.');
										window.location.href='question-main.php';
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