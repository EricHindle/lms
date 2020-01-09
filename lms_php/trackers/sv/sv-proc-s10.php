<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
sec_session_start();

if (login_check($mypdo) == true) {
    if (isset($_SESSION['svid'], $_SESSION['svsec'])) {
        $survey_id = sanitize_int($_SESSION['svid']);
        $current_section = sanitize_int($_SESSION['svsec']);
        unset($_SESSION['svid']);
        unset($_SESSION['svsec']);
        if ($survey_id && $current_section == 10) {
            $tally_sql = "SELECT answer_value from answers WHERE svchks_id = :id";
            $tally_query = $mypdo->prepare($tally_sql);
            $tally_query->execute(array(
                ':id' => $survey_id
            ));
            $tally_count = $tally_query->rowCount();
            if ($tally_count > 0) {
                $tally_fetch = $tally_query->fetchALL(PDO::FETCH_ASSOC);
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
                foreach ($tally_fetch as $key => $value) {
                    $answerCheck = in_array($value['answer_value'], $expectedyn, true);
                    if ($answerCheck == false) {
                        break;
                    }
                }
                if ($answerCheck) {
                    date_default_timezone_set('Europe/London');
                    $phptime = time();
                    $mysqltime = date("Y-m-d H:i:s", $phptime);
                    $currentperiod = get_current_period($phptime, $mypdo);
                    $numscore = 0;
                    $percscore = 0.0;
                    $questionsAnswered = 0;
                    foreach ($tally_fetch as $key => $value) {
                        if ($value['answer_value'] != 'NA') {

                            $questionsAnswered ++;
                            switch ($value['answer_value']) {
                                case 'Yes':
                                    $numscore += 5;
                                    break;
                                case 'No':
                                    $numscore += 0;
                                    break;
                                case '0':
                                    $numscore += 0;
                                    break;
                                case '1':
                                    $numscore += 1;
                                    break;
                                case '2':
                                    $numscore += 2;
                                    break;
                                case '3':
                                    $numscore += 3;
                                    break;
                                case '4':
                                    $numscore += 4;
                                    break;
                                case '5':
                                    $numscore += 5;
                                    break;
                                case 'Done':
                                    $numscore += 5;
                                    break;
                            }
                        }
                    }
                    if ($numscore > 0) {
                        $percscore = round((($numscore / ($questionsAnswered * 5)) * 100), 2);
                    }
                    $updateSql = "UPDATE svchks SET datecomplete = :datecomplete, score = :score, veperiod = :veperiod, lastcompsection = :lastcompsection  WHERE id = :id";
                    $updateQuery = $mypdo->prepare($updateSql);
                    $updateQuery->execute(array(
                        ':datecomplete' => $mysqltime,
                        ':score' => $percscore,
                        ':veperiod' => $currentperiod,
                        ':lastcompsection' => 10,
                        ':id' => $survey_id
                    ));
                    $updateRows = $updateQuery->rowCount();
                    if ($updateRows > 0) {
                        $pull_sql = "SELECT * FROM svchks WHERE id = :id LIMIT 1";
                        $pull_query = $mypdo->prepare($pull_sql);
                        $pull_query->execute(array(
                            ':id' => $survey_id
                        ));
                        $pull_rows = $pull_query->rowCount();
                        if ($pull_rows > 0) {
                            $pull_fetch = $pull_query->fetch(PDO::FETCH_ASSOC);

								$push_sql= "INSERT INTO svchkscomp (id, num, shop, division, region, area, cluster, datestart, datecomplete, completedby, cem, score, na, lat, lon, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11, q12, q13, q14, q15, q16, q17, q18, q19, q20, q21, q22, q23, q24, q25, q26, q27, q28, q29, q30, q31, q32, q33, q34, q35, q36, q37, q38, q39, q40 ,q41, q42, q43, q44, q45, q46, q47, q48, q49, q50 ,q51, q52, q53, q54, q55, q56, q57, q58, q59, q60 ,q61, q62, q63, q64, q65, q66, q67, q68, q69, q70 ,q71, q72, q73, q74, q75, q76, q77, q78, q79, q80, s1, s2, s3, s4, s5, s6, s7, s8, s9, comment1, comment2, comment3, comment4, comment5, comment6, comment7, comment8, comment9, veperiod) VALUES (:ID, :num, :shop, :division, :region, :area, :cluster, :datestart, :datecomplete, :completedby, :cem, :score, :na, :lat, :lon, :q1, :q2, :q3, :q4, :q5, :q6, :q7, :q8, :q9, :q10, :q11, :q12, :q13, :q14, :q15, :q16, :q17, :q18, :q19, :q20, :q21, :q22, :q23, :q24, :q25, :q26, :q27, :q28, :q29, :q30, :q31, :q32, :q33, :q34, :q35, :q36, :q37, :q38, :q39, :q40 ,:q41, :q42, :q43, :q44, :q45, :q46, :q47, :q48, :q49, :q50 ,:q51, :q52, :q53, :q54, :q55, :q56, :q57, :q58, :q59, :q60 ,:q61, :q62, :q63, :q64, :q65, :q66, :q67, :q68, :q69, :q70 ,:q71, :q72, :q73, :q74, :q75, :q76, :q77, :q78, :q79, :q80, :s1, :s2, :s3, :s4, :s5, :s6, :s7, :s8, :s9, :comment1, :comment2, :comment3, :comment4, :comment5, :comment6, :comment7, :comment8, :comment9, :veperiod)";
                            $push_query = $mypdo->prepare($push_sql);
								$push_query->execute(array(':ID'=>null, ':num'=>$pull_fetch['num'], ':shop'=>$pull_fetch['shop'], ':division'=>$pull_fetch['division'], ':region'=>$pull_fetch['region'], ':area'=>$pull_fetch['area'], ':cluster'=>$pull_fetch['cluster'], ':datestart'=>$pull_fetch['datestart'], ':datecomplete'=>$pull_fetch['datecomplete'], ':completedby'=>$pull_fetch['completedby'],  ':cem'=>$pull_fetch['cem'], ':score'=>$pull_fetch['score'], ':na'=>$pull_fetch['na'], ':lat'=>$pull_fetch['lat'], ':lon'=>$pull_fetch['lon'], ':q1'=>$pull_fetch['q1'] , ':q2'=>$pull_fetch['q2'] , ':q3'=>$pull_fetch['q3'] , ':q4'=>$pull_fetch['q4'] , ':q5'=>$pull_fetch['q5'] , ':q6'=>$pull_fetch['q6'] , ':q7'=>$pull_fetch['q7'] , ':q8'=>$pull_fetch['q8'] , ':q9'=>$pull_fetch['q9'] , ':q10'=>$pull_fetch['q10'] , ':q11'=>$pull_fetch['q11'] , ':q12'=>$pull_fetch['q12'] , ':q13'=>$pull_fetch['q13'] , ':q14'=>$pull_fetch['q14'] , ':q15'=>$pull_fetch['q15'] , ':q16'=>$pull_fetch['q16'] , ':q17'=>$pull_fetch['q17'] , ':q18'=>$pull_fetch['q18'] , ':q19'=>$pull_fetch['q19'] , ':q20'=>$pull_fetch['q20'] , ':q21'=>$pull_fetch['q21'] , ':q22'=>$pull_fetch['q22'] , ':q23'=>$pull_fetch['q23'] , ':q24'=>$pull_fetch['q24'] , ':q25'=>$pull_fetch['q25'] , ':q26'=>$pull_fetch['q26'] , ':q27'=>$pull_fetch['q27'] , ':q28'=>$pull_fetch['q28'] , ':q29'=>$pull_fetch['q29'] , ':q30'=>$pull_fetch['q30'] , ':q31'=>$pull_fetch['q31'] , ':q32'=>$pull_fetch['q32'] , ':q33'=>$pull_fetch['q33'] , ':q34'=>$pull_fetch['q34'] , ':q35'=>$pull_fetch['q35'] , ':q36'=>$pull_fetch['q36'] , ':q37'=>$pull_fetch['q37'] , ':q38'=>$pull_fetch['q38'] , ':q39'=>$pull_fetch['q39'] , ':q40'=>$pull_fetch['q40'] , ':q41'=>$pull_fetch['q41'] , ':q42'=>$pull_fetch['q42'] , ':q43'=>$pull_fetch['q43'] , ':q44'=>$pull_fetch['q44'] , ':q45'=>$pull_fetch['q45'] , ':q46'=>$pull_fetch['q46'] , ':q47'=>$pull_fetch['q47'] , ':q48'=>$pull_fetch['q48'] , ':q49'=>$pull_fetch['q49'] , ':q50'=>$pull_fetch['q50'] , ':q51'=>$pull_fetch['q51'] , ':q52'=>$pull_fetch['q52'] , ':q53'=>$pull_fetch['q53'] , ':q54'=>$pull_fetch['q54'] , ':q55'=>$pull_fetch['q55'] , ':q56'=>$pull_fetch['q56'] , ':q57'=>$pull_fetch['q57'] , ':q58'=>$pull_fetch['q58'] , ':q59'=>$pull_fetch['q59'] , ':q60'=>$pull_fetch['q60'] , ':q61'=>$pull_fetch['q61'] , ':q62'=>$pull_fetch['q62'] , ':q63'=>$pull_fetch['q63'] , ':q64'=>$pull_fetch['q64'] , ':q65'=>$pull_fetch['q65'] , ':q66'=>$pull_fetch['q66'] , ':q67'=>$pull_fetch['q67'] , ':q68'=>$pull_fetch['q68'] , ':q69'=>$pull_fetch['q69'] , ':q70'=>$pull_fetch['q70'] , ':q71'=>$pull_fetch['q71'] , ':q72'=>$pull_fetch['q72'] , ':q73'=>$pull_fetch['q73'] , ':q74'=>$pull_fetch['q74'] , ':q75'=>$pull_fetch['q75'] , ':q76'=>$pull_fetch['q76'] , ':q77'=>$pull_fetch['q77'] , ':q78'=>$pull_fetch['q78'] , ':q79'=>$pull_fetch['q79'] , ':q80'=>$pull_fetch['q80'] ,':s1'=>$pull_fetch['s1'], ':s2'=>$pull_fetch['s2'], ':s3'=>$pull_fetch['s3'], ':s4'=>$pull_fetch['s4'], ':s5'=>$pull_fetch['s5'], ':s6'=>$pull_fetch['s6'], ':s7'=>$pull_fetch['s7'],  ':s8'=>$pull_fetch['s8'], ':s9'=>$pull_fetch['s9'],  ':comment1'=>$pull_fetch['comment1'],  ':comment2'=>$pull_fetch['comment2'],  ':comment3'=>$pull_fetch['comment3'],  ':comment4'=>$pull_fetch['comment4'],  ':comment5'=>$pull_fetch['comment5'],  ':comment6'=>$pull_fetch['comment6'],  ':comment7'=>$pull_fetch['comment7'],  ':comment8'=>$pull_fetch['comment8'],  ':comment9'=>$pull_fetch['comment9'], ':veperiod'=>$pull_fetch['veperiod']));
                            $lastInsert = $mypdo->lastInsertId('id');
                            if ($lastInsert) {
                                $ans_sql = "UPDATE answers SET svchkscomp_id = :compid where svchks_id = :id";
                                $ans_query = $mypdo->prepare($ans_sql);
                                $ans_query->execute(array(
                                    ':compid' => $lastInsert,
                                    ':id' => $survey_id
                                ));

                                $lbos_sql = "UPDATE lbos SET lastsvcomplete = :datecomplete, lastsvcheckid = :lastinsert, svperiod = :veperiod WHERE num = :num";
                                $lbos_query = $mypdo->prepare($lbos_sql);
                                $lbos_query->execute(array(
                                    ':datecomplete' => $pull_fetch['datecomplete'],
                                    ':lastinsert' => $lastInsert,
                                    ':veperiod' => $currentperiod,
                                    ':num' => $pull_fetch['num']
                                ));
                                $lbos_count = $lbos_query->rowCount();
                                if ($lbos_count > 0) {
                                    $tidy_sql = "UPDATE svchks SET iscomplete = :iscomplete, fordelete = :fordelete WHERE id = :id";
                                    $tidy_query = $mypdo->prepare($tidy_sql);
                                    $tidy_query->execute(array(
                                        ':iscomplete' => 1,
                                        ':fordelete' => 1,
                                        ':id' => $survey_id
                                    ));
                                    $tidy_rows = $tidy_query->rowCount();
                                    if ($tidy_rows > 0) {
                                        header('Location: sv-success.php?m=1&s=' . $lastInsert);
                                    } else {
                                        header('Location: sv-success.php?m=2&s=0');
                                    }
                                } else {
                                    header('Location: sv-success.php?m=2&s=0');
                                }
                            } else {
                                header('Location: sv-success.php?m=2&s=0');
                            }
                        } else {
                            bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not found in DB 2");
                        }
                    } else {
                        bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not UPDATED in DB");
                    }
                } else {
                    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Unexpected data");
                }
            } else {
                bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Data not found in DB");
            }
        } else {
            bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Suspicious data");
        }
    } else {
        bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Session data not set");
    }
} else {
    bootOut($myPath, $user = $_SESSION['fname'], basename($_SERVER['PHP_SELF']), "Couldn't verify user");
}
?>
