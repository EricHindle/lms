<?php
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

?>