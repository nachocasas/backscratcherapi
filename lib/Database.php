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
    $conn_string = "host={$this->host} port={$this->port} dbname={$this->database} user={$this->user} password={$this->pass}";
    $this->conn = pg_connect($conn_string);

    if(!$this->conn){
      throw new Exception("Unable to connect to database");
    }
  }

  public function query($query){
    $q = pg_query($this->conn, $query);

    if(!$q){
      throw new Exception("Error on query!: ". mysqli_error($this->conn));
    }

    $result = pg_fetch_row($q);
    return $result;
  }


  public function fetch($query){
      $q = pg_query($this->conn, $query);

      if(!$q){
        throw new Exception("Error on fetch query");
      }

      $result = pg_fetch_all($q);

      return $result;
  }

  public function escapeStr($str){
    return pg_escape_string($this->conn, $str);
  }

}