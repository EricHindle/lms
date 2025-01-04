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

function get_api_url($season, $league, $key)
{
    $url = get_global_value($key);
    $url = str_replace("^1", $season, $url);
    $url = str_replace("^2", $league, $url);
    return $url;
}

function get_api_league_id($leagueAbbr)
{
    $apiLeagueId = -1;
    $league = get_league_from_abbr($leagueAbbr);
    if ($league) {
        $apiLeagueId = $league['lms_league_api_id'];
    }
    return $apiLeagueId;
}

function get_team_id_by_api_id($apiteamid)
{
    $teamid = -1;
    global $mypdo;
    $teamsql = "SELECT TOP 1 * FROM lms_team WHERE lms_team_api_id = :apiId";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->bindParam(":apiId", $apiteamid, PDO::PARAM_INT);
    $teamquery->execute();
    $teamfetch = $teamquery->fetch(PDO::FETCH_ASSOC);
    if ($teamquery->rowCount() == 1) {
        $teamid = $teamfetch['lms_team_id'];
    }
    return $teamid;
}

function get_league_fixtures($leagueId, $log)
{
    global $myPath;
    $api_url = get_api_url(get_global_value('api_season'), $leagueId, 'api_fixtures_url');
    $league_fixtures = get_api_data_by_curl($api_url, $log);

    return $league_fixtures;
}

function get_league_teams($leagueId, $log)
{
    global $myPath;
    $api_url = get_api_url(get_global_value('api_season'), $leagueId, 'api_teams_url');
    fwrite($log, "Url : " . $api_url . "\n");
    $league_teams = get_api_data_by_curl($api_url, $log);
    return $league_teams;
}

function get_api_data_by_curl($api_url, $log)
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

function get_team_by_name($name)
{
    global $mypdo;
    $teamsql = "SELECT * FROM lms_team WHERE lms_team_name = :name;";
    $teamquery = $mypdo->prepare($teamsql);
    $teamquery->execute(array(
        ':name' => $name
    ));
    return $teamquery->fetchAll(PDO::FETCH_ASSOC);
}

function insert_team($teamname, $abbr, $apiid)
{
    global $mypdo;
    $sqladdteam = "INSERT INTO lms_team (lms_team_name, lms_team_active, lms_team_abbr, lms_team_api_id) VALUES (:teamname, 1, :abbr, :apiid)";
    $stmtaddteam = $mypdo->prepare($sqladdteam);
    $stmtaddteam->bindParam(":abbr", $abbr);
    $stmtaddteam->bindParam(":teamname", $teamname);
    $stmtaddteam->bindParam(':apiid', $apiid, PDO::PARAM_INT);
    $stmtaddteam->execute();
    $insertcount = $stmtaddteam->rowCount();
    return $insertcount;
}

function update_team($teamid, $teamname, $abbr, $apiid)
{
    global $mypdo;
    $sqladdteamleague = "UPDATE lms_team SET lms_team_name = :name, lms_team_abbr = :abbr, lms_team_api_id = :apiid, lms_team_active = 1 WHERE lms_team_id = :teamid;";
    $stmtaddteamleague = $mypdo->prepare($sqladdteamleague);
    $stmtaddteamleague->bindParam(':teamid', $teamid, PDO::PARAM_INT);
    $stmtaddteamleague->bindParam(":abbr", $abbr);
    $stmtaddteamleague->bindParam(":name", $teamname);
    $stmtaddteamleague->bindParam(':apiid', $apiid, PDO::PARAM_INT);
    $stmtaddteamleague->execute();
    $updatecount = $stmtaddteamleague->rowCount();
    return $updatecount;
}

function delete_league_team_for_team($teamid)
{
    global $mypdo;
    $delsql = "DELETE FROM lms_league_team WHERE lms_league_team_team_id =:teamid";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":teamid", $teamid, PDO::PARAM_INT);
    $delquery->execute();
}

function delete_league_team_for_league($leagueid)
{
    global $mypdo;
    $delsql = "DELETE FROM lms_league_team WHERE lms_league_team_league_id =:leagueid";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":leagueid", $leagueid, PDO::PARAM_INT);
    $delquery->execute();
}

function insert_team_league($teamid, $leagueid)
{
    global $mypdo;
    $sqladdteamleague = "INSERT INTO lms_league_team (lms_league_team_league_id, lms_league_team_team_id) VALUES (:leagueid, :teamid)";
    $stmtaddteamleague = $mypdo->prepare($sqladdteamleague);
    $stmtaddteamleague->bindParam(':teamid', $teamid, PDO::PARAM_INT);
    $stmtaddteamleague->bindParam(':leagueid', $leagueid, PDO::PARAM_INT);
    $stmtaddteamleague->execute();
    $insertcount = $stmtaddteamleague->rowCount();
    return $insertcount;
}

function remove_abbr_for_team($teamid, $abbr)
{
    global $mypdo;
    $abbrsql = "DELETE FROM lms_team_abbr WHERE lms_team_abbr_team_id = :teamid AND lms_team_abbr_abbr = :abbr;";
    $abbrquery = $mypdo->prepare($abbrsql);
    $abbrquery->bindParam(':teamid', $teamid, PDO::PARAM_INT);
    $abbrquery->bindParam(":abbr", $abbr);
    $abbrquery->execute();
    return $abbrquery->rowCount();
}

function insert_team_abbr($teamid, $abbr)
{
    global $mypdo;
    $abbrsql = "INSERT INTO lms_team_abbr (lms_team_abbr_abbr, lms_team_abbr_team_id)
        VALUES (:abbr, :teamid);";
    $abbrquery = $mypdo->prepare($abbrsql);
    $abbrquery->bindParam(':teamid', $teamid, PDO::PARAM_INT);
    $abbrquery->bindParam(":abbr", $abbr);
    $abbrquery->execute();
    return $abbrquery->rowCount();
}

function get_teams_by_abbr($abbr)
{
    global $mypdo;
    $abbrsql = "SELECT * FROM lastmanl_lms.lms_team_abbr WHERE lms_team_abbr_abbr = :abbr;";
    $abbrquery = $mypdo->prepare($abbrsql);
    $abbrquery->bindParam(":abbr", $abbr);
    $abbrquery->execute();
    return $abbrquery->fetchAll(PDO::FETCH_ASSOC);
}



?>