<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'struct/game/game-functions.php';

function get_player($playerid){
    
    global $mypdo;
    $playersql = "SELECT * FROM lms_player WHERE lms_player_id = :id";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->execute(array(
        ':id' => $playerid
    ));
    $playerfetch = $playerquery->fetch(PDO::FETCH_ASSOC);
    return $playerfetch;
}

function notify_loser($playerid, $gameid) {
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'The team you picked this week in the LMS game ' . $gamename . ' was a loser.' ;
    $subject = 'For you, the game is over';
    sendmail($playeremail, $subject, $body, '', $bcclist, $playeremail, $playername);

}

function notify_winner($playerid, $gameid) {
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'The team you picked this week in the LMS game ' . $gamename . ' was a winner. There is nobody else left in the game so you are the winner.' ;
    $subject = 'Congratulations, you are the winner';
    sendmail($playeremail, $subject, $body, '', $bcclist, $playeremail, $playername);
    
}

function notify_joint_winner($playerid, $gameid) {
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'The team you picked this week in the LMS game ' . $gamename . ' was a loser. But everybody else lost too. So all the remaining players from last week are joint winners.' ;
    $subject = 'Congratulations, you are a winner';
    sendmail($playeremail, $subject, $body, '', $bcclist, $playeremail, $playername);
    
}





?>