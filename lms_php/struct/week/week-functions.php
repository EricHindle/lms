<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';

function get_week_state($weekno)
{
    global $mypdo;
    $weekstate = 0;
    $lookupsql = "SELECT lms_week_state FROM lms_week WHERE lms_week_no = :weekno LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':weekno' => $weekno
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $weekstate = $lookup['lms_week_state'];
    }
    return $weekstate;
}

function set_week_state($weekid, $newstate)
{
    global $mypdo;
    $upsql = "UPDATE lms_week SET lms_week_state = :newstate WHERE lms_week_no = :weekid";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':weekid', $weekid);
    $upquery->bindParam(':newstate', $newstate, PDO::PARAM_INT);
    $upquery->execute();
    $upcount = $upquery->rowCount();
    return $upcount;
}

function update_complete($matchweek) {
    global $mypdo;
    $upsql = "UPDATE lms_info SET lms_info_value = :infovalue WHERE lms_info_id = 'match_week_complete'";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':infovalue', $matchweek);
    $upquery->execute();
    $upcount = $upquery->rowCount();
    return $upcount;
}

?>