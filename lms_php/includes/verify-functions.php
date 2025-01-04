<?php
/*
 * HINDLEWARE
 * Copyright (C) 2025 Eric Hindle. All rights reserved.
 */
require 'db_connect.php';

date_default_timezone_set('Europe/London');

function getemailverify($code)
{
    global $mypdo;
    $sql = "SELECT * FROM lastmanl_lms.lms_verify where lms_verify_code = :code";
    $stmt = $mypdo->prepare($sql);
    $stmt->bindParam(':code',$code);
    $stmt->execute();
    $verify = '';
    if ($stmt->rowCount() == 1) {
         $verify = $stmt->fetch(PDO::FETCH_ASSOC);
    } 
    return $verify;
}

function update_player_verified($code, $playerid)
{
    global $mypdo;
    $phptime = time();
    $mysqltime = date("Y-m-d H:i:s", $phptime);
    $sql = "UPDATE lms_verify SET lms_verify_date = :date, lms_verify_ok = 1 WHERE (lms_verify_code = :code)";
    $stmt = $mypdo->prepare($sql);
    $stmt->bindParam(':date',$mysqltime);
    $stmt->bindParam(':code',$code);
    $stmt->execute();
    
    $sql = "UPDATE lms_player SET lms_active = '1', lms_player_email_verified = '1' WHERE (lms_player_id = :playerid)";
    $stmt = $mypdo->prepare($sql);
    $stmt->bindParam(':playerid',$playerid, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
}

?>
