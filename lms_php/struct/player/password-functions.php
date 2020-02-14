<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require_once $myPath . 'struct/player/player-lookup.php';


function createtemppassword($email)
{
    $newpwd = generate_password();
    $hash = password_hash($newpwd, PASSWORD_DEFAULT, [
        'cost' => 10
    ]);
    $player = get_player_by_userid($email);
    if ($player) {
        $playerid = $player['lms_player_id'];
        $istempalready = gettemppasswordcount($playerid);
        if ($istempalready == 1) {
            removetemppassword($playerid);
        }
        inserttemppassword($playerid, $hash);
        sendpasswordemail($player, $newpwd);
    }
}

function gettemppasswordcount($playerid){
    global $mypdo;
    $temppwdsql = "SELECT * FROM lms_player_temp_password WHERE lms_player_id = :id LIMIT 1";
    $temppwdquery = $mypdo->prepare($temppwdsql);
    $temppwdquery->execute(array(
        ':id' => $playerid
    ));

    return $temppwdquery->rowCount();;
}

function inserttemppassword($playerid, $hash)
{
    global $mypdo;
    $inssql = "INSERT INTO lms_player_temp_password (lms_player_id, lms_player_temp_password) VALUES (:player, :hash);";
    $insquery = $mypdo->prepare($inssql);
    $insquery->bindParam(':player', $playerid, PDO::PARAM_INT);
    $insquery->bindParam(':hash', $hash);
    $insquery->execute();
}

function sendpasswordemail($player, $newpwd)
{
    
    sendemailusingtemplate('temppassword', $player['lms_player_id'], '', array($newpwd), false);
}

?>