<?php
/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'struct/email/email-functions.php';

function notify_loser($playerid, $gameid)
{
    sendemailusingtemplate('teamlose', $playerid, $gameid, 0, 0, '', true);
}

function notify_winner($playerid, $gameid)
{
    sendemailusingtemplate('teamwin', $playerid, $gameid, 0, 0, '', true);
}

function notify_postponed($playerid, $gameid)
{
    sendemailusingtemplate('postponed', $playerid, $gameid, 0, 0, '', true);
}

function notify_no_pick($playerid, $gameid)
{
    sendemailusingtemplate('nopick', $playerid, $gameid, 0, 0, '', true);
}

?>