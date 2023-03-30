<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'includes/lookup-functions.php';

sec_session_start();
$currentPage = '';
$formKey = new formKey();

if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            $leagueid = 1;
            if (isset($_POST['leagueid'])) {
                $leagueid = sanitize_int($_POST['leagueid']);
            }
            $leaguerow = get_league($leagueid);
            $leaguename = $leaguerow['lms_league_name'];
            $html = "";
            $thisweek = $_SESSION['matchweek'];
            $lastweek = $thisweek - 1;
            $leaguerows = get_all_leagues(true);
            $resultssql = "SELECT * FROM v_lms_results WHERE lms_match_ha = 'h' AND lms_match_league = :leagueid AND (lms_match_weekno = :thisweek OR lms_match_weekno = :lastweek);";
            $resultsquery = $mypdo->prepare($resultssql);
            $resultsquery->bindParam(":thisweek", $thisweek);
            $resultsquery->bindParam(":lastweek", $lastweek);
            $resultsquery->bindParam(":leagueid", $leagueid, PDO::PARAM_INT);
            $resultsquery->execute();
            $resultsfetch = $resultsquery->fetchAll(PDO::FETCH_ASSOC);

            $key = $formKey->outputKey();
            $weekno = 0;
            $matchdate = date_create("01-01-0001");

            echo '
			<!doctype html>
			<html>
    			<head>
     			    <meta charset="UTF-8">
    			    <title>Results - Last Man Live</title>
    			    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="stylesheet" href="' . $myPath . 'css/style.css" type="text/css">
                    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    			    <script src="' . $myPath . 'js/jquery.js"></script>
    			    <script src="' . $myPath . 'js/jquery.tablesorter.js"></script>
    			    <script>
    		            $(function(){
    		            $(\'#keywords\').tablesorter();
    		            });
    		        </script>
    			</head>
    			        
				<body>';
            include $myPath . 'globNAV.php';
            $html .= '      <div class="container">';
            $first = true;

            $html .= '<div class="box" style="padding:1em;margin:10px;">
                        <form role="form" name ="showresults" method="post" action="show-results.php">';
            $html .= $key;
            $html .= '
                            <div class="form-group" style="margin:12px">
                                <select onchange="this.form.submit()" class="form-dropdown" id="leagueid" name="leagueid" style="margin-bottom: 0px;">
                                    <option value="-1">Select a League</option>';
            foreach ($leaguerows as $myLeague) {
                $html .= '          <option value="' . $myLeague['lms_league_id'] . '">' . $myLeague['lms_league_name'] . '</option>';
            }
            $html .= '	        </select>
                            </div>
                        </form>
                    </div>';
            if ($resultsfetch) {
                $html .= ' <div  class="box" style="padding:1em;margin: 20px;text-align:center;">
<h2>' . $leaguename . '<h2>
                                        </div>';
                foreach ($resultsfetch as $result) {
                    if (! $first && $weekno != $result['lms_match_weekno']) {
                        $html .= '    </table>
                      </div>';
                    }

                    $dispweekno = substr_replace($result['lms_match_weekno'], "/", 4, 0);
                    $dispweekno = substr_replace($dispweekno, "-", 2, 0);
                    if ($weekno != $result['lms_match_weekno']) {
                        $html .= '<div  class="game-card" style="margin-bottom: 20px;">
                         <table style="padding-bottom: 3em;" class="game-table">
                                <tr>
                                    <th colspan="2" border="0">
                                    <div><h2>Week ' . $dispweekno . '</h2></div>
                                    <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                    </th>
                                </tr>';
                    }

                    if (! $first && $matchdate != $result['lms_match_date']) {
                        $html .= '          <tr>
                                    <td>&nbsp;</td>
                                </tr>';
                    }
                    if ($matchdate != $result['lms_match_date']) {
                        $html .= '          <tr>
                                    <td>' . date_format(date_create($result['lms_match_date']), 'd M Y') . '</td>
                                </tr>';
                    }

                    $hometeam = $result['home_team_name'];
                    $awayteam = $result['away_team_name'];
                    $homescore = $result['home_score'];
                    $awayscore = $result['away_score'];

                    $html .= '              <tr style="height: 25px;">
                                    <td style="width:40%;height: 25px;text-align:right;">' . $hometeam . '</td>';

                    if ($result['no_result'] == 1) {
                        $html .= '              <td colspan="3" style="height: 25px;text-align:center;">' . $result['home_result_type'] . ' </td>';
                    } else {
                        $html .= '              <td style="width:5%;height: 25px;text-align:right;">  ' . $homescore . ' </td>
                                    <td style="height: 25px;text-align:center;"> - </td>
                                    <td style="width:5%;height: 25px;">  ' . $awayscore . '  </td>';
                    }

                    $html .= '                  <td  style="width:40%;height: 25px;">' . $awayteam . '</td>
                                </tr>';

                    $first = false;
                    $weekno = $result['lms_match_weekno'];
                    $matchdate = $result['lms_match_date'];
                }

                $html .= '          </table>
                        </div>';
            } else {
                $html .= '<div  class="box" style="padding:1em;margin: 20px;text-align:center;">
                            <h2>' . $leaguename . '<h2>
                            <h3> No results available</h3>
                         </div>';
            }

            $html .= '</div>
                </body>
            </html>';
            echo $html;
        }
    } else {
        header('Location: ' . $myPath . 'index.php?error=1');
    }
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}
?>