<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
function get_team($teamid)
{
    global $mypdo;
    $teamsql = "SELECT * FROM lms_team WHERE lms_team_id = :id LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":id", $teamid, PDO::PARAM_INT);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    return $teamfetch;
}

?>