<?php 
// This will be used for decrypting our aes-128-cbc encrypted texts
define('KAP_SECURE_KEY', '4k+6H4htiuJXc7xtG0IGx0fXGKAUgg/yHHptN2rNMtk=');

/**
 * Encrypts a text 
 * using AES-128-CBC
 * 
 * @param string $text The text to be encrypted
 * @param string $key The key that will be used for encrypting
 * @return string The encrypted text
 * @link https://www.php.net/manual/en/function.openssl-encrypt.php
 */
function kap_encrypt($text, $key = KAP_SECURE_KEY) {
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);

    $ciphertext_raw = openssl_encrypt($text, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
    $encrypted = base64_encode($iv . $hmac . $ciphertext_raw);

    return $encrypted;
}

/**
 * Decrypts an AES-128-CBC
 * encrypted text
 * 
 * @param string $text The encrypted text to be decrypted
 * @param string $key The key that will be used to decrypt the text
 * @return string|bool The decrypted text or false if failed
 * @link https://www.php.net/manual/en/function.openssl-encrypt.php
 */
function kap_decrypt($text, $key = KAP_SECURE_KEY) {
    $encrypted = base64_decode($text);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($encrypted, 0, $ivlen);

    $hmac = substr($encrypted, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($encrypted, $ivlen + $sha2len);

    $decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

    if(hash_equals($hmac, $calcmac))
        return $decrypted;
    else
        return false;
}

?>