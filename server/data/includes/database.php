<?php


class Database {
  private $host = "127.0.0.1";
  private $port = 3306;
  private $user = "root";
  private $pass = "";

  private $dbname = null;

  private $conn = null;

  public $connected = null;

  private $messages = array();

  private $pdo_cursor_fwdonly = array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY);

  function __construct($host = "127.0.0.1", int $port = 3306, string $user = "root", string $pass = "", string $dbname = null)
  {
    $this->host = $host;
    $this->port = $port;

    $this->user = $user;
    $this->pass = $pass;
  }


  function get_errors()
  {
    $errors = array();

    for ($i=0; $i < count($this->messages); $i++) { 
      if ($this->messages[$i]['sts'] === 'ERROR')
        array_push($errors, $this->messages[$i]["msg"]);
    }

    return $errors;
  }

  function set_dbname($dbname)
  {
    $this->dbname = $dbname;
  }

  function get_messages()
  {
    return $this->messages;
  }


  function hasMessages()
  {
    return count( $this->messages ) > 0;
  }

  function hasErrors()
  {
    for ($i=0; $i < count($this->messages); $i++) { 
      if ($this->messages[$i]['sts'] === 'ERROR')
        return true;
    }
    return false;
  }

  function connect()
  {
    try
    {

      // init PDO
      $this->conn = new PDO("mysql:host=$this->host", $this->user, $this->pass);

      // error handling
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      array_push($this->messages, array(
        'sts'=>'SUCCESS', 
        'msg' => 'Connected succesfully'
      ));

      $this->connected = false;// is connected to mysql, but not to the database

    }
    catch (PDOException $e)
    {
      array_push($this->messages, array(
        'sts'=>'ERROR',
        'msg' => $e->getMessage()
      ));
    }

    if ($this->connected === false)
    {
      $this->connectToDatabase();
    }
  }

  function close()
  {
    $this->conn = null;
  }


  private function connectToDatabase()
  {
    try
    {

      $sql = "USE `$this->dbname`";

      $count = $this->conn->exec($sql);
      $this->addSuccess("'$sql' executed with success");

    }
    catch (PDOException $e)
    {
      $this->addError($e->getMessage());
    }
  }

  private function addMessage($msg = array())
  {
    array_push($this->messages, $msg);
  }

  private function addSuccess($msg = "")
  {
    $this->addMessage(array('sts'=>'SUCCESS','msg'=>$msg));
  }

  private function addError($msg = '')
  {
    $this->addMessage(array('sts'=>'ERROR','msg'=>$msg));
  }



  // database actions
  // user handler
  function searchUserID($username = "")
  {
    $sql = "SELECT `id` FROM `users` WHERE (`username` = :username OR `useremail` = :username) AND `userstatus` = '1' LIMIT 1";
    $result = null;

    try
    {

      $sth = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
      $sth->execute(array('username' => $username));

      if ($res = $sth->fetch(PDO::FETCH_OBJ))
      {
        $result = $res->id;
      }
      else
      {
        $this->addError("No user founded on '$sql' ");
      }

    }
    catch (PDOException $e)
    {
      $this->addError($e->getMessage());
    }

    return $result;
  }

  function valiateUserPassword($id, $pass)
  {
    $sql = "SELECT `id` FROM `users` WHERE `id` = :id AND `userpass` = :pass";

    $result = false;

    try
    {

      $sth = $this->conn->prepare($sql, $this->pdo_cursor_fwdonly);
      $sth->execute(array(':id'=>$id,':pass'=>$pass));

      if ($res = $sth->fetch())
      {
        $result = true;
      }

    }
    catch(PDOException $e)
    {
      $this->addError($e->getMessage());
    }

    return $result;
  }


  function saveToken($token, $clientip, $userid)
  {
    $date = date("Y-m-d H:i:s");

    $sql = "INSERT INTO `tokens` (`token`,`clientip`,`userid`,`datetime`) VALUES (:token, :ip, :id, :date)";

    try
    {

      $sth = $this->conn->prepare($sql, $this->pdo_cursor_fwdonly);
      return $sth->execute(array(
        ':token' => $token,
        ':ip' => $clientip,
        ':id' => $userid,
        ':date' => $date
      ));

    }
    catch(PDOException $e)
    {
      $this->addError($e->getMessage());

      return false;
    }
  }
}