<?php

include_once dirname(__FILE__) . '/init.php';

// check for session

// token format:
  // sha1 ip:userid

$c_token = isset( $_COOKIE['token'] ) ? $_COOKIE['token'] : '0';
$s_token = isset( $_SESSION['token'] ) ? $_SESSION['token'] : '1';

$currentUrl = urlencode(CurrentUrl());
$serverUrl = ServerHostUrl();
$datehash = hash("sha256", date("dmYHis"));

if ($c_token === '0' && $s_token === '1')
{
  header('Location: ' . $serverUrl . 'login.php?act=login&url=' . $currentUrl . '&pretoken=' . hash('shad56', date("d-m-Y H:i:s")));
  die;
}

if (($c_token === '0' && $s_token !== '1') || ($c_token !== '0' && $s_token === '1'))
{
  header('Location: ' . $serverUrl . 'login.php?act=newtoken&url=' . $currentUrl . '&pretoken=' . $datehash);
  die;
}