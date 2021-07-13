<?php

/*
 * HINDLEWARE
 * Copyright (C) 2020 Eric Hindle. All rights reserved.
 */
function combobulate($text, $type, $iv)
{
    $returnval = '';
    $passphrase = 'hindlewarepassphrase';
    $cipher = 'aes-128-cbc';
  

    if (in_array($cipher, openssl_get_cipher_methods())) {
        if ($type == "e") {
            $ivlen = openssl_cipher_iv_length($cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($text, $cipher, $passphrase, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
            $returnval = base64_encode( $iv.$hmac.$ciphertext_raw );
        }
        if ($type == "d") {
            $c = base64_decode($text);
            $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len=32);
            $ciphertext_raw = substr($c, $ivlen+$sha2len);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $passphrase, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
            $returnval = $original_plaintext;
        }
    }
    return $returnval;
}
?>