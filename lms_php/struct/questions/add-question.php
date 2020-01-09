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
            if (isset($_POST['question'], $_POST['info'], $_POST['section'], $_POST['type'])) {
                $question = $_POST['question'];
                $info = $_POST['info'];
                $type = $_POST['type'];
                $acttype = '';
                $section = $_POST['section'];
                $html = "";
                if ($question && $type && $section) {
                    
                    switch ($type) {
                        case 'yes/no/na':
                            $acttype = 'yn';
                            break;
                        case 'yes/no':
                            $acttype = 'yn2';
                            break;
                        case 'score/na':
                            $acttype = 'num';
                            break;
                        case 'score':
                            $acttype = 'num2';
                            break;
                        case 'done':
                            $acttype = 'done';
                            break;
                    }

                    date_default_timezone_set('Europe/London');
                    $phptime = time();
                    $mysqltime = date("Y-m-d H:i:s", $phptime);
                    $sqladdquestion = "INSERT INTO questions (section, type, text, info) VALUES (:section, :type, :text, :info)";
                    $stmtaddquestion = $mypdo->prepare($sqladdquestion);
                    $stmtaddquestion->execute(array(
                        ':section' => $section,
                        ':type' => $acttype,
                        ':text' => $question,
                        ':info' => $info
                    ));
                    $added = $stmtaddquestion->rowCount();
                    $html .= "<script>
											alert('" . $added . " questions added.');
											window.location.href='question-main.php';
										</script>";
                } else {
                    $html .= "<script>
										alert('There was a problem. Please check details and try again.');
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
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>