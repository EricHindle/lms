<?php
$myPath = '../../';

require $myPath . 'includes/db_connect.php';
date_default_timezone_set('Europe/London');

function get_player_status($code)
{
    global $mypdo;
    $text = "code missing";
    $lookupsql = "SELECT lms_game_player_status_id, lms_game_player_status_text FROM lms_game_player_status WHERE lms_game_player_status_id = :id LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':id' => $code
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $text = $lookup['lms_game_player_status_text'];
    }
    return $text;
}

function get_game_status($code)
{
    global $mypdo;
    $text = "code missing";
    $lookupsql = "SELECT lms_game_status_id, lms_game_status_text FROM lms_game_status WHERE lms_game_status_id = :id LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':id' => $code
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $text = $lookup['lms_game_status_text'];
    }
    return $text;
}
?>