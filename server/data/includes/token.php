<?php

const openssl_cipher = "AES-128-CTR";
$iv_length = openssl_cipher_iv_length(openssl_cipher);
const iv = '1234567891011121';

function CreateToken($ip, $userid)
{
  $date = date("Y-m-d H:i:s");

  $json = json_encode(array(
    'ip'=> $ip,
    'user' => $userid,
    'date' => $date
  ));

  $encrypt = openssl_encrypt($json, openssl_cipher, ABSPATH, 0, iv);

  return $encrypt;
}

function ReadToken($token)
{
  $decrypt = openssl_decrypt($token, openssl_cipher, ABSPATH, 0, iv);

  return json_decode($decrypt);
}