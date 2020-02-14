<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';

require $myPath . 'includes/db_connect.php';

function get_team_from_match($matchid)
{
    global $mypdo;
    $teamid = 0;
    $lookupsql = "SELECT lms_match_team FROM lms_match WHERE lms_match_id = :id LIMIT 1";
    $lookupquery = $mypdo->prepare($lookupsql);
    $lookupquery->execute(array(
        ':id' => $matchid
    ));
    $lookupcount = $lookupquery->rowCount();
    if ($lookupcount == 1) {
        $lookup = $lookupquery->fetch(PDO::FETCH_ASSOC);
        $teamid = $lookup['lms_match_team'];
    }
    return $teamid;
}

function get_count_of_matches_with_no_result () {
    global $mypdo;
    $selectsql = "SELECT lms_match_id, lms_team_name FROM v_lms_match WHERE lms_match_weekno = :weekno and lms_match_result = ''";
    $selectquery = $mypdo->prepare($selectsql);
    $selectquery->execute(array(
        ':weekno' => $_SESSION['matchweek']
    ));
    $selectcount = $selectquery->rowCount();

    return $selectcount;
    
    
}

?>