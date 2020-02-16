<?php
require_once 'db_connect.php';

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

function check_password($username, $password)
{
    global $mypdo;
    $sql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player WHERE lms_player_login = :username LIMIT 1";
    $query = $mypdo->prepare($sql);
    $query->execute(array(
        ':username' => $username
    ));
    $fetch = $query->fetch(PDO::FETCH_ASSOC);
    if ($fetch) {
        $db_password = $fetch['lms_player_password'];
        $check = password_verify($password, $db_password);
        if ($check) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
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
    $sql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email, lms_access, lms_active FROM lms_player WHERE lms_player_login = :username LIMIT 1";
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

function get_current_period_start()
{
    $phptime = time();
    return date("Y-m", $phptime) . '-01 00:00:00';
}

function get_global_value($valuename)
{
    global $mypdo;
    $infosql = "SELECT lms_info_value FROM lms_info WHERE lms_info_id = :valname";
    $infoquery = $mypdo->prepare($infosql);
    $infoquery->bindParam(':valname', $valuename);
    $infoquery->execute();
    $infofetch = $infoquery->fetch(PDO::FETCH_ASSOC);
    return $infofetch['lms_info_value'];
}

function set_global_value($valuename, $infovalue)
{
    global $mypdo;
    $upsql = "UPDATE lms_info SET lms_info_value = :infovalue WHERE lms_info_id = :id";
    $upquery = $mypdo->prepare($upsql);
    $upquery->bindParam(':id', $valuename);
    $upquery->bindParam(':infovalue', $infovalue);
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

?>
