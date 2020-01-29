<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'struct/game/game-functions.php';
require $myPath . 'includes/mail-util.php';

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
    $body = 'The team you picked this week in the LMS game ' . $gamename . ' was a loser. Sorry but you are out of the game' ;
    $subject = 'For you, the game is over';
    sendmail($playeremail, $subject, $body, '', $bcclist, $playeremail, $playername);

}

function notify_winner($playerid, $gameid) {
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'The team you picked this week in the LMS game ' . $gamename . ' was a winner. You are still in the game. Do not forget to make a pick for the new week.' ;
    $subject = 'You are still in the game';
    sendmail($playeremail, $subject, $body, '', $bcclist, $playeremail, $playername);
    
}

function notify_no_pick($playerid, $gameid) {
    $player = get_player($playerid);
    $playeremail = $player['lms_player_email'];
    $game = get_game($gameid);
    $gamename = $game['lms_game_name'];
    $bcclist = '';
    $body = 'You have failed to make a pick in LMS game ' . $gamename . ' this week. Sorry but you are out of the game' ;
    $subject = 'You missed out';
    sendmail($playeremail, $subject, $body, '', $bcclist, $playeremail, $playername);
    
}





?>