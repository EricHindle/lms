<?php
$myPath = '../../';
require $myPath . 'includes/db_connect.php';
require $myPath . 'struct/email/email-functions.php';

function notify_loser($playerid, $gameid)
{
    sendemailusingtemplate('teamlose', $playerid, $gameid, '');
}

function notify_winner($playerid, $gameid)
{
    sendemailusingtemplate('teamwin', $playerid, $gameid, '');
}

function notify_no_pick($playerid, $gameid)
{
    sendemailusingtemplate('nopick', $playerid, $gameid, '');
}

?>