<?php
/*
 * HINDLEWARE
 * Copyright (C) 2022 Eric Hindle. All rights reserved.
 */
require 'db_connect.php';
require 'info_functions.php';

date_default_timezone_set('Europe/London');

function isSecure()
{
    $docker_is_secure = getenv('FSV_SECURE');
    if ($docker_is_secure == "true") {
        return true;
    } elseif ($docker_is_secure == "false") {
        return false;
    }
    // if env variable not set, detect
    return (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
}

function sec_session_start()
{
    $session_name = 'ret_session_id';
    $secure = isSecure();
    // $secure = true; //USUALLY TRUE but false for retail-lamp
    $httponly = true;
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        error_log("ini_set error in functions", 0);
        header("Location: ../index.php?error=1");
        exit();
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    session_name($session_name);
    session_start();
    session_regenerate_id(true);
}

function gettemppassword($playerid)
{
    global $mypdo;
    $temppwdsql = "SELECT * FROM lms_player_temp_password WHERE lms_player_id = :id LIMIT 1";
    $temppwdquery = $mypdo->prepare($temppwdsql);
    $temppwdquery->execute(array(
        ':id' => $playerid
    ));
    $temppwd = $temppwdquery->fetch(PDO::FETCH_ASSOC);
    return $temppwd;
}

function removetemppassword($playerid)
{
    global $mypdo;
    $delsql = "DELETE FROM lms_player_temp_password WHERE lms_player_id=:player";
    $delquery = $mypdo->prepare($delsql);
    $delquery->bindParam(":player", $playerid, PDO::PARAM_INT);
    $delquery->execute();
}

function login($username, $password, $mypdo)
{
    $sql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_player_mobile, lms_access, lms_active 
                FROM lms_player 
                WHERE lms_player_login = :username OR lms_player_mobile = :username LIMIT 1";
    $query = $mypdo->prepare($sql);
    $query->execute(array(
        ':username' => $username
    ));
    $fetch = $query->fetch(PDO::FETCH_ASSOC);

    if ($fetch) {
        $user_id = $fetch['lms_player_id'];
        $username = $fetch['lms_player_login'];
        $db_password = $fetch['lms_player_password'];
        $fname = $fetch['lms_player_forename'];
        $sname = $fetch['lms_player_surname'];
        $email = $fetch['lms_player_email'];
        $mobile = $fetch['lms_player_mobile'];
        $nickname = $fetch['lms_player_screen_name'];
        $retaccess = $fetch['lms_access'];
        $isactive = $fetch['lms_active'];
        // check if the account is locked
        if ($isactive == 0) {
            return false;
        }

        // Check if the password in the database matches

        $check = password_verify($password, $db_password);
        $temppwd = gettemppassword($user_id);

        if (! $check) {
            If ($temppwd) {
                $db_password = $temppwd['lms_player_temp_password'];
                $check = password_verify($password, $db_password);
            }
        }

        if ($check) {
            // Password is correct!
            removetemppassword($user_id);
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['fname'] = $fname;
            $_SESSION['sname'] = $sname;
            $_SESSION['email'] = $email;
            $_SESSION['mobile'] = $mobile;
            $_SESSION['nickname'] = $nickname;
            $_SESSION['retaccess'] = $retaccess;
            if (getenv('HTTP_X_REAL_IP')) {
                $ip = getenv('HTTP_X_REAL_IP');
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $userAgent = getenv('HTTP_USER_AGENT');

            $hash = password_hash($ip . $userAgent, PASSWORD_DEFAULT, [
                'cost' => 5
            ]);
            $_SESSION['login_string'] = $hash;

            date_default_timezone_set('Europe/London');
            $phptime = time();
            $mysqltime = date("Y-m-d H:i:s", $phptime);

            // Login successful.
            return true;
        } else {

            // FAILED LOGIN
            $now = time();
            $sql2 = "INSERT INTO loginattempts(userid, time) VALUES (:user_id, :time)";
            $stmt2 = $mypdo->prepare($sql2);
            $stmt2->execute(array(
                ':user_id' => $user_id,
                ':time' => $now
            ));
            return false;
        }
    } else {
        return false;
    }
}

function login_check($mypdo)
{
    $err = false;
    if ($err == false) {
        if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'], $_SESSION['fname'], $_SESSION['sname'], $_SESSION['email'], $_SESSION['nickname'])) {
            $user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];
            $username = $_SESSION['username'];
            if (getenv('HTTP_X_REAL_IP')) {
                $ip = getenv('HTTP_X_REAL_IP');
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            $userAgent = getenv('HTTP_USER_AGENT');
            $login_check = $ip . $userAgent;
            $check = password_verify($login_check, $login_string);
            date_default_timezone_set('Europe/London');
            $phptime = time();
            $mysqltime = date("Y-m-d H:i:s", $phptime);

            if ($check) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function sanitize_paranoid_string($string, $min = '', $max = '')
{
    $string = preg_replace("/[^a-zA-Z0-9]/", "", $string);
    $len = strlen($string);
    if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
        return FALSE;
    }

    return $string;
}

function sanitize_email_string($string, $min = '', $max = '')
{
    $string = preg_replace("/[^a-zA-Z0-9@.\ ]/", "", $string);
    $len = strlen($string);
    if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
        return FALSE;
    }

    return $string;
}

function sanitize_message_string($string, $min = '', $max = '')
{
    $string = preg_replace("/[^a-zA-Z0-9\_&\ ]/", "", $string);
    $len = strlen($string);
    if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
        return FALSE;
    }

    return $string;
}

function sanitize_int($integer, $min = '', $max = '')
{
    $int = intval($integer);
    if ((($min != '') && ($int < $min)) || (($max != '') && ($int > $max))) {
        return FALSE;
    }

    return $int;
}

function sanitize_dashed_number($string, $min = '', $max = '')
{
    $string = preg_replace("/[^0-9\-]/", "", $string);
    $len = strlen($string);
    if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
        return FALSE;
    }

    return $string;
}

function sanitize_datetime($string, $min = '', $max = '')
{
    $string = preg_replace("/[^0-9\-&\ \:]/", "", $string);
    $len = strlen($string);
    if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
        return FALSE;
    }

    return $string;
}

function sanitize_location($string, $min = '', $max = '')
{
    $string = preg_replace("/[^a-zA-Z0-9-.\-&\ ]/", "", $string);
    $len = strlen($string);
    if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
        return FALSE;
    }

    return $string;
}

function get_current_period($phptime, $mypdo)
{
    $mytime = date("Y-m-d", $phptime);
    $periodsql = "SELECT period FROM periods WHERE startdate <= :mydate AND stopdate >= :mydate LIMIT 1";
    $periodquery = $mypdo->prepare($periodsql);
    $periodquery->execute(array(
        ':mydate' => $mytime
    ));
    $periodfetch = $periodquery->fetch(PDO::FETCH_ASSOC);
    return $periodfetch['period'];
}

function get_result_type($resulttype, $mypdo)
{
    $rtsql = "SELECT * FROM lastmanl_lms.lms_result_type WHERE lms_result_type = :type LIMIT 1";
    $rtquery = $mypdo->prepare($rtsql);
    $rtquery->execute(array(
        ':type' => $resulttype
    ));
    $rtfetch = $rtquery->fetch(PDO::FETCH_ASSOC);
    return $rtfetch;
}
function get_all_result_types($mypdo)
{
    $rtsql = "SELECT * FROM lastmanl_lms.lms_result_type";
    $rtquery = $mypdo->prepare($rtsql);
    $rtquery->execute();
    $rtfetch = $rtquery->fetchall(PDO::FETCH_ASSOC);
    return $rtfetch;
}

function get_current_period_start()
{
    $phptime = time();
    return date("Y-m", $phptime) . '-01 00:00:00';
}

function get_global_value($valuename)
{
    global $mypdo;
    $infosql = "SELECT lms_info_value, lms_info_enc FROM lms_info WHERE lms_info_id = :valname";
    $infoquery = $mypdo->prepare($infosql);
    $infoquery->bindParam(':valname', $valuename);
    $infoquery->execute();
    $infofetch = $infoquery->fetch(PDO::FETCH_ASSOC);
    $infovalue = $infofetch['lms_info_value'];
    if ($infofetch['lms_info_enc'] == 1) {
        $infovalue = decrypt($infofetch['lms_info_value']);
    }
    return $infovalue;
}

function set_global_value($valuename, $infovalue, $enc)
{
    $infoenc = 0;
    if ($enc) {
        $infovalue = encrypt($infovalue);
        $infoenc = 1;
    }
    global $mypdo;
    $upsql = "UPDATE lms_info SET lms_info_value = :infovalue, lms_info_enc = :enc WHERE lms_info_id = :id";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':id', $valuename);
    $upquery->bindParam(':infovalue', $infovalue);
    $upquery->bindParam(':enc', $infoenc);
    $upquery->execute();
    return $upquery->rowCount();
}

function bootOut($rootPath, $user = 'UNKNOWN', $page = 'UNKNOWN', $error = 'UNKNOWN')
{
    error_log("User: " . $user . ", Page: " . $page . ", Error: " . $error, 0);
    return header('Location: ' . $rootPath . 'index.php?error=1');
}

function generate_password()
{
    $allchars = "abcdefghjkmnpqrstuvwxyz23456789ABCDEFGHJKLMNOPQRSTUVWXYZ";
    $randstr = str_shuffle($allchars);
    $passcode = "";
    for ($i = 1; $i < 9; $i ++) {
        $passcode .= substr($randstr, 0, 1);
        $randstr = str_shuffle($randstr);
    }
    return $passcode;
}

function get_ekey()
{
    $pre = get_global_value('hw_pre_length');
    $post = get_global_value('hw_post_length');
    return get_combobulator_a($pre, $post);
}

function get_eiv()
{
    $pre = get_global_value('hw_pre_length');
    $post = get_global_value('hw_post_length');
    return get_combobulator_b($pre, $post);
}

function get_key()
{
    $pre = get_global_value('hw_pre_length');
    $post = get_global_value('hw_post_length');
    $dkeye = get_global_value('hw_1') . get_global_value('hw_2');
    $dkeyd = combobulate(substr($dkeye, $pre, strlen($dkeye) - $pre - $post), 'd', get_ekey(), get_eiv());
    return $dkeyd;
}

function get_iv()
{
    $pre = get_global_value('hw_pre_length');
    $post = get_global_value('hw_post_length');
    $dive = get_global_value('hw_3') . get_global_value('hw_4');
    $divd = combobulate(substr($dive, $pre, strlen($dive) - $pre - $post), 'd', get_ekey(), get_eiv());
    return $divd;
}

function decrypt($encstring)
{
    $decstring = $encstring;
    if ($_SESSION['encrypted']) {
        $decstring = combobulate($encstring, 'd', $_SESSION['hwkey'], $_SESSION['hwiv']);
    }
    return $decstring;
}

function encrypt($decstring)
{
    $encstring = $decstring;
    if ($_SESSION['encrypted']) {
        $encstring = combobulate($decstring, 'e', $_SESSION['hwkey'], $_SESSION['hwiv']);
    }
    return $encstring;
}

function get_selection_start_date($gamestartweek)
{
    global $mypdo;
    $previousweek = $gamestartweek - 1;
    $weeksql = "SELECT lms_week_start FROM lms_week WHERE lms_week_no = :weekno LIMIT 1";
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(":weekno", $previousweek, PDO::PARAM_INT);
    $weekquery->execute();
    $weekfetch = $weekquery->fetch(PDO::FETCH_ASSOC);
    return $weekfetch['lms_week_start'];
}

function get_player_by_email($email)
{
    global $mypdo;
    $playersql = "SELECT lms_player_id FROM lms_player WHERE lms_player_email = :email LIMIT 1";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->execute(array(
        ':email' => $email
    ));
    $player = $playerquery->fetch(PDO::FETCH_ASSOC);
    return $player;
}

function get_player_by_mobile($mobile)
{
    global $mypdo;
    $playersql = "SELECT lms_player_id FROM lms_player WHERE lms_player_mobile = :mobile LIMIT 1";
    $playerquery = $mypdo->prepare($playersql);
    $playerquery->execute(array(
        ':mobile' => $mobile
    ));
    $player = $playerquery->fetch(PDO::FETCH_ASSOC);
    return $player;
}
function get_calendar_row($calid)
{
    global $mypdo;
    $calsql = "SELECT * FROM lms_calendar WHERE lms_calendar_id = :cal LIMIT 1";
    $calquery = $mypdo->prepare($calsql);
    $calquery->bindParam(':cal', $calid, PDO::PARAM_INT);
    $calquery->execute();
    $calrow = $calquery->fetch(PDO::FETCH_ASSOC);
    return $calrow;
}

function get_all_calendars()
{
    global $mypdo;
    $calsql = "SELECT * FROM lms_calendar ORDER BY lms_calendar_season, lms_calendar_name";
    $calquery = $mypdo->prepare($calsql);
    $calquery->execute();
    $calrows = $calquery->fetchAll(PDO::FETCH_ASSOC);
    return $calrows;
}

function set_game_session_values($gameid)
{
    global $mypdo;
    $gamesql = "SELECT * FROM v_lms_game WHERE lms_game_id = :gameid LIMIT 1";
    $gamequery= $mypdo->prepare($gamesql);
    $gamequery->bindParam(":gameid", $gameid, PDO::PARAM_INT);
    $gamequery->execute();
    $game = $gamequery->fetch(PDO::FETCH_ASSOC);

    $_SESSION['currentseason'] = $game['lms_calendar_season'];
    $_SESSION['currentweek'] = $game['lms_calendar_current_week'];
    $_SESSION['selectweek'] = $game['lms_calendar_select_week'];
    $currentweek = sprintf('%02d', $_SESSION['currentweek']) ;     
    $selectweek = sprintf('%02d', $_SESSION['selectweek']) ;
    $_SESSION['matchweek'] = $_SESSION['currentseason'] . $currentweek;
    $_SESSION['selectweekkey'] = $_SESSION['currentseason'] . $selectweek;
    $_SESSION['selperiod'] =   $selectweek . '/' .$_SESSION['currentseason'] ;
    $_SESSION['deadline'] = $game['lms_select_deadline'];
    return $game;
}

function get_week_row($year, $week, $cal)
{
    global $mypdo;
    $weeksql = 'SELECT * FROM lastmanl_lms.lms_week where lms_year = :season and lms_week = :week and lms_week_calendar = :cal';
    $weekquery = $mypdo->prepare($weeksql);
    $weekquery->bindParam(':season', $year, PDO::PARAM_INT);
    $weekquery->bindParam(':week', $week, PDO::PARAM_INT);
    $weekquery->bindParam(':cal', $cal, PDO::PARAM_INT);
    $weekquery->execute();
    $weekrow = $weekquery->fetch(PDO::FETCH_ASSOC);
    return $weekrow;
}
?>
