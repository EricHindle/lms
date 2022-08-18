<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
function combobulate($string, $action = 'e')
{
    $secret_key = get_combobulation_key();
    $secret_iv = get_combobulation_iv();
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'e') {
        $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    } else if ($action == 'd') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function get_combobulation_key()
{
    return 'sdvniksdfkivhckbdvkjydouiadsfkbvcib';
}

function get_combobulation_iv()
{
    return 'asdfkiasdkybsdfkiybsdkjbhsdkvhjdfaskjyg';
}
?>