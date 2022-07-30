<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */

$myPath = '../../';
require $myPath . 'includes/db_connect.php';

function remove_abbr_for_team($teamid) {
    global $mypdo;
    $abbrsql = "DELETE FROM lms_team_abbr WHERE lms_team_abbr_team_id = :team;";
    $abbrquery = $mypdo->prepare($abbrsql);
    $abbrquery->execute(array(
        ':team' => $teamid
    ));
    
}

function insert_team_abbr($teamid, $abbr)
{
    global $mypdo;

    $abbrsql = "INSERT INTO lms_team_abbr (lms_team_abbr_abbr, lms_team_abbr_team_id)
        VALUES (:abbr, :team);";
        $abbrquery = $mypdo->prepare($abbrsql);
        $abbrquery->execute(array(
            ':team' => $teamid,
            ':abbr' => $abbr
        ));
    
     return;
}

function get_team_by_abbr($abbr){
    global $mypdo;
    
    $abbrsql = "SELECT * FROM lastmanl_lms.lms_team_abbr WHERE lms_team_abbr_abbr = :abbr;";
    $abbrquery = $mypdo->prepare($abbrsql);
    $abbrquery->execute(array(
        ':abbr' => $abbr
    ));
    return $abbrquery->fetchAll(PDO::FETCH_ASSOC);
}

?>