<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
require $myPath . 'struct/game/game-functions.php';

sec_session_start();
$formKey = new formKey();
$currentPage = '';
if (login_check($mypdo) == true) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (! isset($_POST['form_key']) || ! $formKey->validate()) {
            header('Location: ' . $myPath . 'index.php?error=1');
        } else {
            // if (isset($_POST['gamename'], $_POST['gamestartweek'], $_POST['leagueid'])) {
            if (isset($_POST['gamename'], $_POST['leagueid'])) {
                $gamename = $_POST['gamename'];
                // $gamestartweek = $_POST['gamestartweek'];
                $leagueId = $_POST['leagueid'];
                $leaguesql = "SELECT * FROM v_lms_league_calendar WHERE lms_league_id = :leagueid LIMIT 1";
                $leaguequery = $mypdo->prepare($leaguesql);
                $leaguequery->bindParam(':leagueid', $leagueId);
                $leaguequery->execute();
                $leaguecal = $leaguequery->fetch(PDO::FETCH_ASSOC);
                $startwk = sprintf('%02d', $leaguecal['lms_calendar_select_week']);
                $gamestartweek = $leaguecal['lms_calendar_season'] . $startwk;
                $leaguecurrcal = $leaguecal['lms_league_current_calendar'];
                if ($gamename) {
                    $html = "";
                    $cusql = "SELECT lms_game_id FROM lms_game WHERE lms_game_name = :gamename LIMIT 1";
                    $cuquery = $mypdo->prepare($cusql);
                    $cuquery->bindParam(':gamename', $gamename);
                    $cuquery->execute();
                    $cucount = $cuquery->rowCount();

                    if ($cucount > 0) {
                        $html .= "<script>
										alert('A game with that name already exists.');
										window.location.href='game-manage.php';
									</script>";
                    } else {
                        $playerid = $_SESSION['user_id'];
                        $gamecode = generate_game_code();
                        $sqladdgame = "INSERT INTO lms_game (lms_game_start_wkno, lms_game_name, lms_game_status, lms_game_week_count, lms_game_total_players, lms_game_still_active, lms_game_manager, lms_game_code, lms_game_calendar) 
                                                    VALUES (:startwkno, :gamename, 1, 0, 0, 0, :playerid, :gamecode, :gamecal)";
                        $stmtaddgame = $mypdo->prepare($sqladdgame);
                        $stmtaddgame->bindParam(":startwkno", $gamestartweek);
                        $stmtaddgame->bindParam(":gamename", $gamename);
                        $stmtaddgame->bindParam(":playerid", $playerid, PDO::PARAM_INT);
                        $stmtaddgame->bindParam(":gamecode", $gamecode);
                        $stmtaddgame->bindParam(":gamecal", $leaguecurrcal, PDO::PARAM_INT);
                        $stmtaddgame->execute();
                        $added = $stmtaddgame->rowCount();
                        $gameid = $mypdo->lastInsertId();
                        if ($added == 1) {
                            insert_game_league($gameid, $leagueId);
                            add_player_to_game($gameid, $_SESSION['user_id']);
                            sendemailusingtemplate('newgame', $playerid, $gameid, 0, '', true);
                            $key = $formKey->outputKey();
                            echo '
      <!doctype html>
		<html>
			<head>
 			    <meta charset="UTF-8">
			    <title>LML Games</title>
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
                            $html .= '
                <div class="container">';
                            $html .= $key;
                            $joinmessage = 'Please join my Last Man Live game called ' . $gamename . '. %0D%0A The code is ' . $gamecode . '. %0D%0A';

                            $html .= '
                <div class="game-card"  style="margin-bottom:20px">
                               
                    <table class="game-table" style="padding-bottom:3em">
                        <tr>
                            <th colspan="2" border="0">
                                <div><h2>' . $gamename . '</h2></div>
                                <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                            </th>
                        </tr>
                        <tr>
                            <td width="50%">
                                <div class="table-columnTitle">Start Week</div>
                                <div>' . sprintf('%02d', $gamestartweek) . '</div>
                            </td>

                            <td width="50%">
                                <div class="table-columnTitle">Game Status:</div>
                                <div>Created</div>
                            </td>
                        </tr>
                                
                        <tr>
                            <td width="50%">
                                <div class="table-columnTitle">Game Code:</div>
                                <div class="game-code">
                                    <input class="game-code" style="width:50%;text-align:center" type="text"  id="gamecode" name="gamecode" value="' . $gamecode . '">
                                </div>
                            </td>
                            <td width="50%">
                                <div class="table-columnTitle">Share</div>
                                <div>
                                    <a href="https://twitter.com/share?url=https://lastmanlive.co.uk&text=' . $joinmessage . '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;" target="_blank" title="Share on Twitter">
                                        <img border="0" alt="Twitter" src="' . $myPath . 'img/twitterbutton.png" width="25" height="25">
                                    </a>
                                    <a href="https://wa.me?text=' . $joinmessage . ' https://lastmanlive.co.uk" onClick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600\');return false;" target="_blank" title="Share on whatsapp">
                                        <img border="0" alt="WhatsApp" src="' . $myPath . 'img/whatsapp.png" width="25" height="25">
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </table>
                                 
                </div>
                <div class="game-pick-card">
                    <table class="game-table">
                        <tr>
                            <th>
                                <h2>Invite Players</h2>
                                <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <form class="form-group" role="form" name ="inviteplayer" method="post" action="' . $myPath . 'struct/player/invite-player.php">';
                            $html .= $key;
                            $html .= '
                                    <input type= "hidden" name= "gameid" value="' . $gameid . '" />
                                    <h4>Email Addresses</h4>
                                    <div class="form-group">
                                            <input type="text" class="form-field" name="email1"  id="email1" placeholder="new player 1"> <br>
                                            <input type="text" class="form-field" name="email2"  id="email2" placeholder="new player 2"> <br>
                                            <input type="text" class="form-field" name="email3"  id="email3" placeholder="new player 3"> <br>
                                            <input type="text" class="form-field" name="email4"  id="email4" placeholder="new player 4"> <br>
                                    </div>
                                    <div class="form-group">
                                        <input id="submit" name="submit" type="submit" value="Invite" class="btn">
    					            </div>	
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </body>
        </html>
        ';
                        } else {
                            $html .= "<script>
									alert('Game was not added.');
									window.location.href='game-manage.php';
								  </script>";
                        }
                    }

                    echo $html;
                } else {
                    echo "<script>
								alert('Game name missing. Please check details and try again.');
								window.location.href='game-manage.php';
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