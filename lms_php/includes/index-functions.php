<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022 Eric Hindle. All rights reserved.
 */
date_default_timezone_set('Europe/London');

/* function get_current_deadline_date($selectweekkey, $calid)
{
    global $mypdo;
    $weeksql = "SELECT lms_week_deadline FROM lms_week WHERE lms_week_no = :week AND lms_week_calendar = :cal LIMIT 1";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $selectweekkey, PDO::PARAM_INT);
    $weekquery->bindParam(":cal", $calid, PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetch(PDO::FETCH_ASSOC);
    return $weekfetch['lms_week_deadline'];
} */

function get_current_matchweek()
{
    global $mypdo;
    $weeksql = "SELECT * FROM lms_week WHERE lms_week_no = :week";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $_SESSION['matchweek'], PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
    return $weekfetch;
}

function get_state($weekno)
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

function process_week_end()
{
    global $mypdo;
    $today = date("Y-m-d");
    $weeksql = "SELECT * FROM lms_week WHERE lms_week_start = :today LIMIT 1";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":week", $today);
    $weekquery->execute();
    $rowcount = $weekquery->rowCount();
    if ($rowcount == 1) {
        $weekfetch = $weekquery->fetchAll(PDO::FETCH_ASSOC);
        $actualmatchwk = $weekfetch['lms_week_no'];
        if ($actualmatchwk > $_SESSION['matchweek']) {}
    }
}

?>