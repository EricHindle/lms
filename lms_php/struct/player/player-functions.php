<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';

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


?>