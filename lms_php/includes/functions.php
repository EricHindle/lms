<?php
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
    //$secure = true; //USUALLY TRUE but false for retail-lamp
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

function login($username, $password, $mypdo) {
	$sql = "SELECT lms_player_id, lms_player_login, lms_player_password, lms_player_forename, lms_player_surname, lms_player_screen_name, lms_player_email FROM lms_player WHERE lms_player_login = :username LIMIT 1";
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
        $email =  $fetch['lms_player_email'];
        $nickname = $fetch['lms_player_screen_name'];
        // check if the account is locked
        // from too many login attempts

        if (checkbrute($user_id, $mypdo) == true) {
            // TO DO
            // Account is locked
            // Send an email to user saying their account is locked + reset link
            // need email adresses
            return false;
        } else {
            // Check if the password in the database matches

            $check = password_verify($password, $db_password);
            if ($check) {
                // Password is correct!
                $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                $_SESSION['user_id'] = $user_id;
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
                $_SESSION['username'] = $username;
                $fname = preg_replace("/[^a-zA-Z0-9_\-\ ]+/", "", $fname);
                $_SESSION['fname'] = $fname;
                $sname = preg_replace("/[^a-zA-Z0-9_\-\ ]+/", "", $sname);
                $_SESSION['sname'] = $sname;
                $email = preg_replace("/[^a-zA-Z0-9_\-\ ]+/", "", $email);
                $_SESSION['email'] = $email;
                $nickname = preg_replace("/[^a-zA-Z0-9_\-\ ]+/", "", $nickname);
                $_SESSION['nickname'] = $nickname;

				$hash = password_hash($_SERVER['HTTP_X_REAL_IP'] . $_SERVER['HTTP_USER_AGENT'], PASSWORD_DEFAULT,  ['cost' => 5]);
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
				$stmt2->execute(array(':user_id' => $user_id, ':time' => $now));
                return false;
            }
        }
    } else {
        return false;
    }
}

function checkbrute($user_id, $mypdo) {
    return false;
    // $now = time();

    // $valid_attempts = $now - (2 * 60 * 60);
    // $sql3 = "SELECT time FROM loginattempts WHERE userid = :user_id AND time > :time";
    // $stmt = $mypdo->prepare($sql3);
    // $stmt->execute(array(':user_id' => $user_id, ':time' => $valid_attempts));
    // If there have been more than 6 failed logins
    // if ($stmt->rowCount() > 6) {
    // // return true; FIX!!!!!!!!!!!!!!!!!!!!!
    // return false;
    // } else {
    // return false;
    // }
}

function login_check($mypdo) {
    $err = false;
    if ($err == false) {
        if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'], $_SESSION['fname'], $_SESSION['sname'], $_SESSION['email'], $_SESSION['nickname'])) {
            $user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];
            $username = $_SESSION['username'];
            $login_check = $_SERVER['HTTP_X_REAL_IP'] . $_SERVER['HTTP_USER_AGENT'];
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

function bootOut($rootPath, $user = 'UNKNOWN', $page = 'UNKNOWN', $error = 'UNKNOWN')
{
    error_log("User: " . $user . ", Page: " . $page . ", Error: " . $error, 0);
    return header('Location: ' . $rootPath . 'index.php?error=1');
}
?>
