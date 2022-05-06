<?php

include_once dirname(__FILE__) . '/init.php';

$action = isset( $_GET['act'] ) ? $_GET['act'] : 'login';


if ($action === 'newtoken')
{
  $clientip = $_SERVER['REMOTE_ADDR'];
  $userid = 1;
  $token = CreateToken($clientip, $userid);
  // $decT = ReadToken($newT);

  // echo $newT . '    ' . print_r($decT);

  // save token on database
  if (!$db->saveToken($token, $clientip, $userid))
    die ( json_encode( $db->get_errors() ) );
  
  setcookie('token', $token, time() + 60);
  $_SESSION['token'] = $token;

  $red = isset( $_GET['url'] ) ? urldecode( $_GET['url'] ) : ServerHostUrl();

  header("Location: $red");

  die;
}


$id = $db->searchUserID('alesontor@gmail.com');
if ($id != null)
{
  $p = "7905ea63eb5b482b57c43b79c77fb3278c7e14f5";

  if (!$db->valiateUserPassword($id,$p))
  {
    die(json_encode(array('sts' => 'ERR', 'cd' => 'WRONG_PASSWORD', 'msg' => 'Invalid password')));
  }
}


if ($db->hasMessages())
{
  echo json_encode($db->get_messages());
}

$db->close();