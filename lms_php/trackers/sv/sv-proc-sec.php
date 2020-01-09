<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "CSRF failure");
    } else {
        if (login_check($mypdo) == true) {
            $nonapplicable = 0;
            if (isset($_POST['id'])) {
                $svchkSql = "SELECT na FROM svchks WHERE id = :id";
                $svchkQuery = $mypdo->prepare($svchkSql);
                $svchkQuery->execute(array(
                    ':id' => ($_POST['id'])
                ));
                $svchkFetch = $svchkQuery->fetch(PDO::FETCH_ASSOC);
                $nonapplicable = $svchkFetch['na'];
            }
            $current_section = sanitize_int($_SESSION['svsec']);
            $sections_sql = "SELECT * from sections where sections_id = :section LIMIT 1";
            $sections_query = $mypdo->prepare($sections_sql);
            $sections_query->execute(array(
                ':section' => ($current_section + 1)
            ));
            $nextSection = $sections_query->rowCount();
            $questions_sql = "SELECT q.text as question, q.info as info, q.section as section, q.questions_id as id, q.type as type from questions q where q.active_question = 1 and q.section = :section";
            $questions_query = $mypdo->prepare($questions_sql);
            $questions_query->execute(array(
                ':section' => $current_section
            ));
            $questions_fetch = $questions_query->fetchAll(PDO::FETCH_ASSOC);
            $questionsAnswered = 0;
            $numscore = 0;
            
            $sectionScore = 0.0;
            foreach ($questions_fetch as $quest) {
                $qid = $quest['id'];
                $ctrlName = 'q' . $qid . 'radio';
                $qnoctrl = 'qno' . $qid;
                if (isset($_POST['id'], $_POST[$ctrlName], $_POST[$qnoctrl])) {
                    $qno = $_POST[$qnoctrl];
                    $id = sanitize_int($_POST['id']);
                    $answer = sanitize_paranoid_string($_POST[$ctrlName]);
                    $expectedyn = array(
                        "Yes",
                        "No",
                        "NA",
                        "0",
                        "1",
                        "2",
                        "3",
                        "4",
                        "5",
                        "Done"
                    );
                    $answerCheck = true;
                    $answerCheck = in_array($answer, $expectedyn, true);
                    if ($answerCheck) {
                        if ($answer == 'NA') {
                            $nonapplicable ++;
                        } else {
                            $tempnum = 0;
                            switch ($answer) {
                                case 'Yes':
                                    $tempnum = 5;
                                    break;
                                case 'No':
                                    $tempnum = 0;
                                    break;
                                case '0':
                                    $tempnum = 0;
                                    break;
                                case '1':
                                    $tempnum = 1;
                                    break;
                                case '2':
                                    $tempnum = 2;
                                    break;
                                case '3':
                                    $tempnum = 3;
                                    break;
                                case '4':
                                    $tempnum = 4;
                                    break;
                                case '5':
                                    $tempnum = 5;
                                    break;
                                case 'Done':
                                    $tempnum = 5;
                                    break;
                            }
                            $numscore += $tempnum;
                            $questionsAnswered ++;
                        }
                        $vesql2 = "INSERT INTO answers (svchks_id, questions_id, answer_value, question_number ) VALUES (:ID, :Qid, :ansValue, :qno)";
                        $vequery2 = $mypdo->prepare($vesql2);
                        $vequery2->execute(array(
                            ':ID' => $id,
                            ':Qid' => $qid,
                            ':ansValue' => $answer,
                            ':qno' => $qno
                        ));
                    }
                }
            }

            $comctrlName = 'comments' . $current_section;
            if (isset($_POST['id'], $_POST[$comctrlName], $_POST['loclat'], $_POST['loclon'])) {
                $id = sanitize_int($_POST['id']);
                $lat = sanitize_location($_POST['loclat']);
                $lon = sanitize_location($_POST['loclon']);
                $comments1 = filter_var($_POST[$comctrlName], FILTER_SANITIZE_STRING);
                if ($numscore > 0) {
                    $sectionScore = round((($numscore / ($questionsAnswered * 5)) * 100), 2);
                }

                if ($current_section < 2) {
                    $updateSql = "UPDATE svchks SET na = :na, lat = :lat, lon = :lon, s" . $current_section . " = :s1, comment" . $current_section . " = :comment, lastcompsection = :lastSection WHERE id = :id";
                    $updateQuery = $mypdo->prepare($updateSql);
                    $updateQuery->execute(array(
                        ':na' => $nonapplicable,
                        ':lat' => $lat,
                        ':lon' => $lon,
                        ':s1' => $sectionScore,
                        ':comment' => $comments1,
                        ':lastSection' => $current_section,
                        ':id' => $id
                    ));
                } else {
                    $updateSql = "UPDATE svchks SET na = :na, s" . $current_section . " = :s1, comment" . $current_section . " = :comment, lastcompsection = :lastSection WHERE id = :id";
                    $updateQuery = $mypdo->prepare($updateSql);
                    $updateQuery->execute(array(
                        ':na' => $nonapplicable,
                        ':s1' => $sectionScore,
                        ':comment' => $comments1,
                        ':lastSection' => $current_section,
                        ':id' => $id
                    ));
                }
                $updateRows = $updateQuery->rowCount();
                if ($updateRows > 0) {
                    $_SESSION['svid'] = $id;
                    $_SESSION['svsec'] = sanitize_int($current_section) + 1;
                    if ($nextSection == 1) {
                        header('Location: ' . $myPath . 'trackers/sv/sv-survey-new.php');
                    } else {
                        header('Location: ' . $myPath . 'trackers/sv/sv-proc-s10.php');
                    }
                } else {
                    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not UPDATED in DB");
                }
            } else {
                bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "POST data missing");
            }
        } else {
            bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Couldn't verify user");
        }
    }
} else {
    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Expected POST request");
}
?>
