<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022 Eric Hindle. All rights reserved.
 */
$myPath = '../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'includes/functions.php';
require $myPath . 'includes/formkey.class.php';
sec_session_start();
$formKey = new formKey();
$playerid = $_SESSION['user_id'];
$gamesql = "SELECT * FROM v_lms_player_games WHERE lms_player_id = :player ORDER BY lms_game_player_status, lms_game_name ASC";
$gamequery = $mypdo->prepare($gamesql);
$gamequery->bindParam(':player', $playerid, PDO::PARAM_INT);
$gamequery->execute();
$gamefetch = $gamequery->fetchAll(PDO::FETCH_ASSOC);
$currentPage = 'games';
$picksql = "SELECT lms_team_name, lms_match_date, lms_week  FROM v_lms_player_picks WHERE lms_pick_player_id = :player and lms_pick_game_id = :game and lms_match_weekno = :matchwk LIMIT 1";
$key = $formKey->outputKey();
$html = '';
if (login_check($mypdo) == true) {
    echo '
    <!doctype html>
        <html>
        	<head> 
                <meta charset="UTF-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        		<link rel="stylesheet" href="' . $myPath . 'css/style.css">
                <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
            </head>
        	<body>          
            ';
    include $myPath . 'globNAV.php';
    echo '
                <div class="container">
        ';
    foreach ($gamefetch as $rs) {
        $gameid = $rs['lms_game_id'];
        $pickquery = $mypdo->prepare($picksql);
        $pickquery->bindParam(':player', $playerid, PDO::PARAM_INT);
        $pickquery->bindParam(':game', $gameid, PDO::PARAM_INT);
        /* Get this week's pick */
        $pickquery->bindParam(':matchwk', $_SESSION['matchweek']);
        $pickquery->execute();
        $matchcount = $pickquery->rowCount();
        $matchweekpick = $pickquery->fetch(PDO::FETCH_ASSOC);
        /* Get next week's pick */
        $pickquery->bindParam(':matchwk', $_SESSION['selectweekkey']);
        $pickquery->execute();
        $selectcount = $pickquery->rowCount();
        $selectweekpick = $pickquery->fetch(PDO::FETCH_ASSOC);

        $showthisweekspick = $rs['lms_game_status'] > 1;
        $shownextweekspick = $rs['lms_game_player_status'] == 1 && $rs['lms_game_status'] < 3;

        $thispick = 'No Pick';
        $calrow = get_calendar_row($rs['lms_game_calendar']);
        
        $gameselectweek = $calrow['lms_calendar_current_week'] + 1;
        
        $weekrow = get_week_row($rs['lms_year'],$gameselectweek,$rs['lms_game_calendar']);
        
        $deadline = $weekrow['lms_week_deadline'];
        $nextpick = '(make a pick by ' . date_format(date_create($deadline), 'd M Y') . ')';
        
        $rowcolor = 'black';
        $playercolor = 'status-playing';
        switch ($rs['lms_game_status']) {
            case 1:
                $rowcolor = 'status-recruiting';
                break;
            case 2:
                $rowcolor = 'status-playing';
                break;
            case 3:
                $rowcolor = 'status-out';
                break;
            case 4:
                $rowcolor = 'cancelled';
                break;
        }
        /*
         * If player is out (or left the game) this week - show the team
         */
        $displaynextweek = '';
        if ($rs['lms_game_player_status'] == 2 or $rs['lms_game_player_status'] == 3 or $rs['lms_game_status'] == 4) {
            $playercolor = $rs['lms_game_player_status'] == 2 ? 'status-out' : 'cancelled';
            if ($rs['lms_game_player_status'] == 2) {
                if ($matchcount > 0) {
                    $thispick = $matchweekpick['lms_team_name'];
                }
            }
        } else {
            if ($rs['lms_game_start_wkno'] > $_SESSION['selectweekkey']) {
                $selectionstart = get_selection_start_date($rs['lms_game_start_wkno']);
                $nextpick = 'Team selection begins ' . date_format(date_create($selectionstart), 'd M Y');
            } else {
                if ($matchcount > 0) {
                    $thispick = $matchweekpick['lms_team_name'];
                }
                if ($selectcount > 0) {
                    $displaynextweek = '(Wk.' . sprintf('%02d', $selectweekpick['lms_week']) . ')';
                    $nextpick = $selectweekpick['lms_team_name'];
                }
            }
        }
        $html .= '
            
                <form class="game-card-form" role="form" name ="showgame" method="post" action="' . $myPath . 'struct/game/show-played-game.php">';
        $html .= $key;
        $html .= '  <button class="game-button" type="submit" name="gameid" value="' . $rs['lms_game_id'] . '">
                        <div class="game-card">
                            <table class="game-table">
                                <tr>
                                    <th colspan="2" border="0">
                                    <div><h2>' . $rs['lms_game_name'] . '</h2></div>
                                    <div id="divider" style="background-color:#CC1417; height: 3px; width:25%; margin-top:2px; margin-bottom:7px;"></div>
                                    </th>
                                </tr>
                                <tr>
                                    <td width="50%">
                                    <div class="table-columnTitle">Your Status</div>
                                    <div>' . $rs['lms_game_player_status_text'] . '</div>
                                    </td>
                                    <td width="50%">
                                    <div class="table-columnTitle">Game Status:</div>
                                    <div>' . $rs['lms_game_status_text'] . '</div>
                                    </td>
                                    <td>
                                    <div class="table-columnTitle">Players</div>
                                    <div>' . $rs['lms_game_still_active'] . ' / ' . $rs['lms_game_total_players'] . '</div>
                                    </td>
                                </tr>';
        if ($showthisweekspick) {
            $html .= '          <tr>
                                    <td colspan="2" class="your-pick-table">
                                        <div class="table-columnTitle">This Week\'s Pick:</div>
                                        <div><h3><b>' . $thispick . '</b></h3></div>';
            if ($matchcount > 0) {
                $html .= '              <div class="table-columnTitle">Fixture: (' . date_format(date_create($matchweekpick['lms_match_date']), 'd M Y') . ')</div> ';
            }
            $html .= '              </td>
                                </tr>';
        }
        $html .= '              <tr>
                                    <td colspan="2" class="your-pick-table">';
        if ($shownextweekspick) {

            $html .= '                  <div class="table-columnTitle">Next Week\'s Pick ' . $displaynextweek . ':</div>
                                        <div><h4>' . $nextpick . '</h4></div>';
            if ($selectcount > 0) {
                $html .= '              <div class="table-columnTitle">Fixture: (' . date_format(date_create($selectweekpick['lms_match_date']), 'd M Y') . ')</div>';
            }
            $html .= '              </td>';
        }
        $html .= '                                
                                    <td>
                                        <img style="width:30px" src="' . $myPath . 'img/icons/PickButton.svg">
                                    </td>
                                </tr>
                            </table>
                            <div class="status-bar ' . $playercolor . '">Your Status: ' . $rs['lms_game_player_status_text'] . '</div>
                            </div>
                        </button>
                    </form>';
    }
    $html .= '
                </div>
            </div>
        </body>
    </html>
    	';
    echo $html;
} else {
    header('Location: ' . $myPath . 'index.php?error=1');
}

?> 
