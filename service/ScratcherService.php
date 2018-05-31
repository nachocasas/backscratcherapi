<?php

require_once __DIR__.'/../dao/ScratcherDAO.php';
require_once __DIR__.'/../model/Scratcher.php';


class ScratcherService {
  
  public static function getAll(){
    try {
      $dao = new ScratcherDAO();
      $result = $dao->getAll();
      $scratchers = array();

      if(!$result){
        return $scratchers;
      }

      foreach ($result as $item){
        $scratcher = new Scratcher($item);
        $scratchers[] = $scratcher->getValues();
      }
      
      return $scratchers;

    } catch (Exception $e){
      return array('error' => 'Error on api response');
    }
  }

  public static function add($values){
    try {
      $scratcher = new Scratcher($values);
      $id = $scratcher->save();

      return array('id' => $id);

    } catch (Exception $e){
      return array('error' => 'Error on add operation');
    }
  }

  public static function update($newValues){
    try {
      $dao = new ScratcherDAO();
      $scratcherValues = $dao->getById($newValues['id']);
      if(!$scratcherValues){
        return array('message' => 'Item id not found');
      }
      $scratcher = new Scratcher($scratcherValues);
      $scratcher->update($newValues);
      $scratcher->save();

      return $scratcher->getValues();

    } catch (Exception $e){
      return array('error' => 'Error on update operation');
    }
  }

  public static function delete($values){
    try {
      $id = $values['id'];
      $dao = new ScratcherDAO();

      if(!$dao->getById($values['id'])){
        return array('message' => 'Item id not found');
      }

      $dao->delete($id);

      return array('message' => 'Item deleted');

    } catch (Exception $e){
      return array('error' => 'Error on delete operation');
    }
  }

}