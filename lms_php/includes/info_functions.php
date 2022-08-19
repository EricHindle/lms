<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
function combobulate($string, $action = 'e', $secret_key, $secret_iv)
{
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
    return 'asdfkisdvniksdfkivhckbdvkjydouiadsfkbvcibskjyg';
}

function get_combobulation_iv()
{
    return 'sdvnikasdfkiasdkybsdfkiybsdkjbhsdkvhjdfaskjygbvcib';
}

function get_combobulator_a($pre, $post)
{
    $hw_a = get_combobulation_key();
    return substr($hw_a, $pre, strlen($hw_a) - $pre - $post);
}

function get_combobulator_b($pre, $post)
{
    $hw_b = get_combobulation_iv();
    return substr($hw_b, $pre, strlen($hw_b) - $pre - $post);
}

?>