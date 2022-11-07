<?php
$myPath = '/home/lastmanl/public_html/';
// $myPath = "../";

/*
 * HINDLEWARE
 * Copyright (C) 2020-22 Eric Hindle. All rights reserved.
 */
function get_api_key()
{
    return get_global_value('api-key');
}

function get_api_url($season, $league)
{
    $url = get_global_value('api_fixtures_url');
    $url = str_replace("^1", $season, $url);
    $url = str_replace("^2", $league, $url);
    return $url;
}

function get_api_league_id($leagueAbbr)
{
    $apiLeagueId = - 1;
    $league = get_league_from_abbr($leagueAbbr);
    if ($league) {
        $apiLeagueId = $league['lms_league_api_id'];
    }
    return $apiLeagueId;
}

function get_team_abbr_by_api_id($apiteamid)
{
    $teamabbr = "";

    global $mypdo;
    $teamsql = "SELECT * FROM lastmanl_lms.lms_team WHERE lms_team_api_id = :apiId LIMIT 1";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":apiId", $apiteamid, PDO::PARAM_INT);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    if ($teamquery->rowCount() == 1) {
        $teamabbr = $teamfetch['lms_team_abbr'];
    }
    return strtoupper($teamabbr);
}

function get_league_fixtures($leagueId, $log)
{
    global $myPath;
    $api_url = get_api_url(get_global_value('api_season'), $leagueId);
    $league_fixtures = get_fixtures_by_curl($api_url, $log);

    return $league_fixtures;
}

function get_fixtures_by_curl($api_url, $log)
{
    fwrite($log, "--- Calling api ---\n");
    $curl = curl_init();

    $headers = array(
        "x-rapidapi-host: v3.football.api-sports.io",
        "x-rapidapi-key: " . get_api_key()
    );

    curl_setopt($curl, CURLOPT_URL, $api_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    $curl_errno = curl_errno($curl);

    $fixtures = "";
    if ($curl_errno == 0) {
        fwrite($log, "--- Decoding the response ---\n");
        $fixtures = json_decode($curl_response);
    } else {
        fwrite($log, "Curl error : " . strval($curl_errno) . "\n");
    }

    curl_close($curl);

    return $fixtures->response;
}

function split_fixtures($fixtures, $status)
{
    $matches = array();

    switch ($status) {
        case "scheduled":
            $statuscodes = array(
                "TBD",
                "NS"
            );
            break;
        case "played":
            $statuscodes = array(
                "FT",
                "AET",
                "PEN"
            );
            break;
        case "not played":
            $statuscodes = array(
                "PST",
                "CANC",
                "ABD"
            );
            break;
    }

    foreach ($fixtures as $fixture) {
        if (in_array($fixture->fixture->status->short, $statuscodes)) {
            $matches[] = $fixture;
        }
    }
    return $matches;
}
?>