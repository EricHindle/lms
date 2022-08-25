<?php
require 'includes/db_connect.php';
require 'includes/functions.php';
require 'includes/formkey.class.php';

sec_session_start();
$formKey = new formKey();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (! isset($_POST['form_key']) || ! $formKey->validate()) {
        header('Location: index.php?error=1');
    } else {
        if (isset($_POST['username'], $_POST['password'])) {
            $_SESSION['hwkey'] = get_key();
            $_SESSION['hwiv'] = get_iv();
            $username = encrypt($_POST['username']);
            $password = $_POST['password'];
            if (strlen($_POST['username']) > 5) {
                if (login($username, $password, $mypdo) == true) {
                    header('Location: menus/home.php');
                } else {
                    header('Location: index.php?error=1');
                }
            } else {
                header('Location: index.php?error=1');
            }
        } else {
            header('Location: index.php?error=1');
        }
    }
} else {
    header('Location: index.php?error=1');
}

?>