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
            if (isset($_POST['questn'])) {
                $id = sanitize_int($_POST['questn']);
                if ($id) {
                    $types = array('yn'=>'Yes/No/NA', 'yn2'=>'Yes/No', 'num'=>'Score/NA', 'num2'=>'Score', 'done'=>'Done' );
                    $html = "";
                    $questionsql = "SELECT text, info, section, type, active_question FROM questions q WHERE q.questions_id = :id";
                    $questionquery = $mypdo->prepare($questionsql);
                    $questionquery->execute(array(
                        ':id' => $id
                    ));
                    $questioncount = $questionquery->rowCount();

                    if ($questioncount > 0) {
                        $sectionSql = "SELECT * FROM sections ORDER BY sections_id ASC";
                        $sectionQuery = $mypdo->prepare($sectionSql);
                        $sectionQuery->execute();
                        $sectionFetch = $sectionQuery->fetchAll(PDO::FETCH_ASSOC);
                        $key = $formKey->outputKey();
                        $questionfetch = $questionquery->fetch(PDO::FETCH_ASSOC);
                        echo '
								<!doctype html>
								<html>
									<head>
										
									    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
									    <meta charset="UTF-8">
									    
									    <title>Edit Question</title>
									    
									    <meta name="viewport" content="width=device-width, initial-scale=1">
									    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
									    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
									    <script src="' . $myPath . 'js/jquery.js"></script>
									    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
									</head>

									<body>';
                        include $myPath . 'globNAV.php';
                        $html .= '
										<section id="homeSection">
									    <br><br>
									        <div class="container">
									        	<div class="row">
									                <div class="col-md-12">
									                    <h1><strong>Edit Question</strong></h1>
									                    <br>
									                </div>
									      		</div>
									        	<div class = "row">';

                        $html .= '			<div class="well col-md-10 col-md-offset-1 textDark">
									                	<form class="form-horizontal" role="form" name ="edit" method="post" action="process-edit-question.php">';
                        $html .= $key;
                        $html .= '					<h3 class="text-center">Edit Question</h3>
									                    	<br>
									                    	<div class="form-group">
																<label class="col-sm-2" for="question">Question:</label>
																<div class="col-sm-12">
																 	<textarea class="form-control" rows="1" name="question">' . $questionfetch['text'] . '</textarea>
																</div>
																<label class="col-sm-2" for="info">Info:</label>
																<div class="col-sm-12">
																 	<textarea class="form-control" rows="4" name="info">' . $questionfetch['info'] . '</textarea>
																</div>
															</div>
															<div class="form-group">
																<label class="col-sm-2" for="type">Type:</label>
																<div class="col-sm-4">
																 	<select class="form-control" id="type" name="type">';

                        foreach($types as $typeid=> $typevalue){
                            $html .= ' <option value="' . $typeid . '" ';
                            if ($typeid == $questionfetch['type']) {
                                $html .= 'selected';
                            }
                            $html .= '>'. $typevalue . '</option>';
                                
                        }

                       $html .= '                     </select>
																</div>
																<label class="col-sm-2" for="section">Section:</label>
																<div class="col-sm-4">
																 	<select class="form-control" id="section" name="section">';
                        foreach ($sectionFetch as $mySection) {
                            if ($mySection['sections_id'] == $questionfetch['section']) {
                                $html .= '<option selected value="' . $mySection['sections_id'] . '">' . $mySection['section_name'] . '</option>';
                            }else{
                                $html .= '<option value="' . $mySection['sections_id'] . '">' . $mySection['section_name'] . '</option>';
                            }
                        }
                        $html .= '	                                </select>
																</div>
                                                             </div>
                                                             <br>
    														 <div class="form-group">
                                                                <label class="col-sm-2" for="activeq">Active question ?</label>
                                                                <div class="col-sm-1">
                                                                    <input type="checkbox" class="form-control" id="activeq" name="activeq" value="active" ';
                        if ($questionfetch['active_question'] == 1){
                            $html .= 'checked';
                        }
                            $html .= '>
                                                                </div>
                                                            </div>
															<input type= "hidden" name= "id" value="' . $id . '" />
										                    <div class="form-group">
										                    	<br>
										                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
										                    </div>
										                </form>
										            </div>
										        </div>
										        <div class="row">
													<br>
													<div class="col-xs-6">
														<a href="' . $myPath . 'struct/questions/question-main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
														<br>
													</div>
												</div>
									      		<br><br><br><br>
									    	</div>
									    </section>
									</body>
								</html>
									            ';
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