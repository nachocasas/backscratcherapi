<?php

require_once __DIR__.'/../lib/Database.php';

class ScratcherDAO {

  private $db;

  public function __construct(){
    try{
      $this->db = new Database();
      $this->db->connect();
    } catch(Exception $e) {
      die("ServiceLayer - Exception on DB connection:" . $e->getMessage());
    }
  }

  public function getAll($limit = 0){
    $query = "SELECT * FROM Scratchers";
    if($limit > 0){
      $query .= " LIMIT 0,{$limit}";
    }
    $result = $this->db->fetch($query);
    return $result;
  }

  
  public function getById($id){
    $id = $this->db->escapeStr($id);
    $query = "SELECT * FROM Scratchers WHERE id = {$id}";
    $result = $this->db->fetch($query);

    return $result[0] ?? null;
  }

  public function insert($values){

    $name = $this->db->escapeStr($values['name']);
    $description = $this->db->escapeStr($values['description']);
    $size = $this->db->escapeStr($values['size']);
    $cost = $values['cost'];

    $query = "INSERT INTO Scratchers (item_name, item_description, item_size, item_cost)VALUES('%s','%s', '%s', '%d')";

    $query = sprintf($query, $name, $description, $size, $cost);

    $lastId = $this->db->query($query);
    
    return $lastId;
  }

  public function update($values){
    $id = $this->db->escapeStr($values['id']);
    $name = $this->db->escapeStr($values['name']);
    $description = $this->db->escapeStr($values['description']);
    $size = $this->db->escapeStr($values['size']);
    $cost = $values['cost'];

    $query = "UPDATE Scratchers SET item_name = '%s', item_description = '%s', item_size = '%s', item_cost = '%d'
              WHERE id = %d";

    $query = sprintf($query, $name, $description, $size, $cost, $id);
    $this->db->query($query);
    
    return true;
  }

  public function delete($id){
    $id = $this->db->escapeStr($id);
    $query = "DELETE FROM Scratchers WHERE id = %d";

    $query = sprintf($query, $id);
    print_r($query);
    $this->db->query($query);
  }

}