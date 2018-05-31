<?php

require_once __DIR__.'/../dao/ScratcherDAO.php';

class Scratcher {

  private $id;
  private $name;
  private $description;
  private $size;
  private $cost;

  public function __construct($values){
    
      $this->setValues($values);
    
      $this->validate();
  }

  private function validate(){
    return true;
  }

  public function update($newValues){
    $resultArr = array_merge($this->getValues(), $newValues);
    $this->setValues($resultArr);
  }

  public function save(){
    $dao = new ScratcherDAO();
    $values = $this->getValues();
    if($this->id){
      return $dao->update($values);
    }
    return $dao->insert($values);
  }

  private function setValues($values){
    $this->id           = $values['id'] ?? null;
    $this->name         = $values['item_name'] ?? $values['name'] ?? null;
    $this->description  = $values['item_description'] ?? $values['description'] ?? null;
    $this->size         = $values['item_size'] ?? $values['size'] ?? null;
    $this->cost         = $values['item_cost'] ?? $values['cost'] ?? null;
  }

  public function getValues(){
    return get_object_vars($this);
  }

}