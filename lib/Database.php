<?php

require_once __DIR__.'/../config.php';

class Database {

  private $host;
  private $port;
  private $user;
  private $pass;
  private $database;
  private $conn;

  public function __construct(){
    global $config;

    $this->host = $config['DB']['host'];
    $this->port = $config['DB']['port'];
    $this->user = $config['DB']['user'];
    $this->pass = $config['DB']['pass'];
    $this->database = $config['DB']['database'];
  }

  public function connect() {
    $this->conn = mysqli_connect($this->host, $this->user, $this->pass);
    $this->conn->select_db($this->database);

    if(!$this->conn){
      throw new Exception("Unable to connect to database");
    }
  }

  public function query($query){
    $q = mysqli_query($this->conn, $query);

    if(!$q){
      throw new Exception("Error on query!: ". mysqli_error($this->conn));
    }

    $result = mysqli_insert_id($this->conn);
    return $result;
  }


  public function fetch($query){
      $q = mysqli_query($this->conn, $query);

      if(!$q){
        throw new Exception("Error on fetch query");
      }
      $result = array();

      while($row = mysqli_fetch_assoc($q)){
        $result[] = $row;
      }

      return $result;
  }

  public function escapeStr($str){
    return mysqli_real_escape_string($this->conn, $str);
  }

}