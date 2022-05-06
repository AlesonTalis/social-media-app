<?php

function CreateNewToken()
{
  $ip = $_SERVER['REMOTE_ADDR'];

  $_SESSION['token'] = sha1($ip);

  return $_SESSION['token'];
}

function NewTokenFromIP()
{
  return sha1($_SERVER['REMOTE_ADDR']);
}

function TokenCompare($token)
{
  $tk = NewTokenFromIP();

  return $tk === $token;
}

function RedirectToLogin()
{
  $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  header("Location: ".ServerHostUrl()."/login.php?action=get_token&url=".urlencode(CurrentUrl()));
}

function CurrentUrl()
{
  return ServerHostUrl()."$_SERVER[REQUEST_URI]";
}

function ServerHostUrl()
{
  return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
}