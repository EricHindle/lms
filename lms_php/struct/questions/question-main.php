<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
if (login_check($mypdo) == true && $_SESSION['retaccess'] == 999) {
    $formKey = new formKey();
    $key = $formKey->outputKey();

    $sectionSql = "SELECT * FROM sections ORDER BY sections_id ASC";
    $sectionQuery = $mypdo->prepare($sectionSql);
    $sectionQuery->execute();
    $sectionFetch = $sectionQuery->fetchAll(PDO::FETCH_ASSOC);

    $questionSql = "SELECT * FROM questions ORDER BY questions_id ASC";
    $questionQuery = $mypdo->prepare($questionSql);
    $questionQuery->execute();
    $questionFetch = $questionQuery->fetchAll(PDO::FETCH_ASSOC);

    $currentSql = "SELECT * FROM questions q LEFT OUTER JOIN sections s ON q.section = s.sections_id WHERE q.active_question = 1 ORDER BY q.section ASC, q.questions_id ASC";
    $currentQuery = $mypdo->prepare($currentSql);
    $currentQuery->execute();
    $currentFetch = $currentQuery->fetchAll(PDO::FETCH_ASSOC);

    $html = "";

    echo '
		<!doctype html>
		<html>
			<head>
				
			    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
			    <meta charset="UTF-8">
			    
			    <title>Question Admin</title>
			    
			    <meta name="viewport" content="width=device-width, initial-scale=1">
			    <link rel="stylesheet" href="' . $myPath . 'css/bootstrap.min.css">
			    <link rel="stylesheet" href="' . $myPath . 'css/rethome.css">
			    <script src="' . $myPath . 'js/jquery.js"></script>
			    <script src="' . $myPath . 'js/bootstrap.min.js"></script>
			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
			    <script>
		            $(function(){
		            $(\'#keywords\').tablesorter(); 
		            });
		        </script>

			</head>

			<body>';
    include $myPath . 'globNAV.php';
    $html .= '
				<section id="homeSection">
			    <br><br>
			        <div class="container">
			        	<div class="row">
			                <div class="col-md-12">
			                    <h1><strong>Question Admin</strong></h1>
			                    <br>
			                </div>
			      		</div>
			        	<div class = "row">';

    $html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="addquestion" method="post" action="add-question.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Add Question</h3>
			                    	<br>
				                    <div class="form-group">
				                    	<label for="question">Question:</label>
				                    	<input type="text" class="form-control" id="question" name="question" maxlength="1000" placeholder="question" /><br>
				                    	<label for="info">Info (MAX 1000):</label>
					                    <textarea class="form-control" rows="4" id="info" name="info" placeholder="info"></textarea><br>
                                        <small>&amp;bull; = &bull;</small><br><br>
					                    <label for="type">Type:</label>
					                    <select class="form-control" id="type" name="type">
						                    <option>yes/no</option>
						                    <option>yes/no/na</option>
						                    <option>score</option>
						                    <option>score/na</option>
					                    </select>
					                    <br>';

    $html .= '	                    <label id="lbl_section" for="section">Section:</label>
			                            <select class="form-control" id="section" name="section">';
    foreach ($sectionFetch as $mySection) {
        $html .= '<option value="' . $mySection['sections_id'] . '">' . $mySection['section_name'] . '</option>';
    }
    $html .= '	                    </select>
			                            ';
    $html .= '	                 
										<br>
   
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '			<div class="well col-md-4 col-md-offset-1 textDark">
			                	<form class="form-horizontal" role="form" name ="editquestion" method="post" action="edit-question.php">';
    $html .= $key;
    $html .= '					<h3 class="text-center">Edit Question</h3>
			                    	<br>
				                    <div class="form-group">
			                        	<label for="questn">Choose Question:</label>
			                            <select class="form-control" id="questn" name="questn">';
    foreach ($questionFetch as $myQuestion) {
        $html .= '<option value="' . $myQuestion['questions_id'] . '">' . $myQuestion['questions_id'] . ':  ' . $myQuestion['text'] . '</option>';
    }
    $html .= '	                    </select>
				                    </div>
				                    <div class="form-group">
				                    	<br>
				                        <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-primary">
				                    </div>
				                </form>
				            </div>
			            ';

    $html .= '		</div>
						<div class = "row">
				        	<div class="well col-md-10 col-md-offset-1 textDark">
				        		<h3>Active Questions</h3>
					        	<table class="table table-bordered" id="keywords">
									<thead>
									<tr class="info">
                                        <th>Id</th>
                                      	<th>Number</th>
										<th>Question</th>
									</tr>
                                    <tr> <td colspan="3"></td></tr>
									</thead>
									<tbody>
									';
    $qno = 0;
    $currentSection = 0;
    foreach ($currentFetch as $rs) {
        if ($currentSection != $rs['section']) {
            $qno = 0;
            $currentSection = $rs['section'];
            $html .= '
									<tr>
                                        <td colspan="3" class="info">' . $rs['section_name'] . '</td>
                                    </tr>';
        }

        $qno += 1;
        if ($rs['active_question'] == 1) {
            $active = 'Yes';
        } else {
            $active = 'No';
        }
        $html .= '
									<tr>
                                        <td>' . $rs['questions_id'] . '</td>
                                        <td>' . $rs['section'] . '.' . $qno . '</td>
										<td>' . $rs['text'] . '</td>
									</tr>';
    }
    $html .= '
									</tbody>
								</table>
							</div>
						</div>

				        ';

    $html .= '	   </div>   		
			      		<div class="row">
							<br>
							<div class="col-xs-6">
								<a href="' . $myPath . 'struct/main.php" class="btn btn-primary btn-lg push-to-bottom" role="button">Back</a>
								<br>
							</div>
						</div>
			      		<br><br><br><br>
			    	</div>
			    </section>
			</body>
		</html>

		';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>
