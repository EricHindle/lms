<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
function get_player($playerid)
{
    global $mypdo;
    $playersql = "SELECT * FROM lms_player WHERE lms_player_id = :id LIMIT 1";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->bindParam(":id", $playerid, PDO::PARAM_INT);
    $playerquery->execute();
    $playerfetch = $playerquery->fetch(PDO::FETCH_ASSOC);
    return $playerfetch;
}

function get_player_by_userid($userid)
{
    global $mypdo;
    $sql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player WHERE lms_player_login = :username LIMIT 1";
    $query = $mypdo->prepare($sql);
    $query->execute(array(
        ':username' => $userid
    ));
    $fetch = $query->fetch(PDO::FETCH_ASSOC);
    return $fetch;
}
?>