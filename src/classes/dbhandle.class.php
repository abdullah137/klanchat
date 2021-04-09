<?php


class DBHandle
{
  private String $db_host;
  private String $db_port;
  private String $db_name;
  private String $db_charset;
  private String $db_user;
  private String $db_pass;
  private $pdo;

  public function __construct()
  {
    $this->db_host = "localhost";
    $this->db_port = "3306";
    $this->db_name = "klanchat";
    $this->db_charset = "utf8mb4";
    $this->db_user = "root";
    $this->db_pass = "OtlPHP07";
  }

  public function connect()
  {
    try
    {
      $this->pdo = new PDO("mysql:host={$this->db_host}; port={$this->db_port}; dbname={$this->db_name}; charset={$this->db_charset};", $this->db_user, $this->db_pass);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $this->pdo;
    }
    catch (PDOException $e)
    {
      echo "PDO: Database Failure... <br>";
      echo $e->getMessage();
    }
  }

  public function close()
  {
    return $this->pdo = NULL;
  }

}


?>
