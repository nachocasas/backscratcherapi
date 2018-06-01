<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization");

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__.'/service/ScratcherService.php';
require_once __DIR__.'/lib/utils.php';
require_once __DIR__.'/lib/Auth.php';

use JsonSchema\Validator;

class Api {
 
  public static function getToken($data){
    $token = Auth::getToken($data['apiId']);
    return array('token' => $token);
  }

  public static function get($headers){

    $result = ScratcherService::getAll();
    return $result;
  }
  
  public static function post($data, $headers){

    $validate = self::validate($data, 'post');
    $result = $validate === true ? ScratcherService::add($data) : array('error' => $validate);

    return $result;
  }

  public static function put($data, $headers){

    $validate = self::validate($data, 'put');
    $result = $validate === true ? ScratcherService::update($data) : array('error' => $validate);
    return $result;
  }
  
  public static function delete($data, $headers){
   
    $validate = self::validate($data, 'delete');
    $result = $validate === true ? ScratcherService::delete($data) : array('error' => $validate);
    return $result;
  }

  private static function validate($data, $file){
    $validator = new JsonSchema\Validator;
    $validator->validate($data, (object)['$ref' => 'file://' . realpath('./schemas/'.$file.'.json')]);
    $violations = array();

    if (!$validator->isValid()) {
      foreach ($validator->getErrors() as $error) {
          $violations[] = sprintf("[%s] %s\n", $error['property'], $error['message']);
      }
      return $violations;
    }
    return true;
  }

}

$requestPath = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$requestBody = file_get_contents('php://input');
$jsonRequest = json_decode($requestBody, true);

if($requestPath[0] == 'getAuth'){

  $result = Api::getToken($jsonRequest);
  echo json_encode($result);

} elseif($requestPath[0] == 'scratchers'){

  $headers = request_headers();

  if(!$headers['AUTHORIZATION']){
    echo json_encode(array('error' => 'Auth header missing'));
    exit();
  }

  if(!Auth::isAuth($headers['AUTHORIZATION'])){
    echo json_encode(array('error' => 'Invalid Token'));
    exit();
  }

  switch($_SERVER['REQUEST_METHOD']){

     /*
    * Method GET
    * Sample Request: <empty>
    * Sample Response:
    * [{
    *      "id": "2",
    *      "name": "samsung3",
    *      "description": "This is a nice description",
    *      "size": "XL",
    *      "cost": "240.00"
    *  }]
    * 
    */
    case 'GET':
      $result = Api::get($headers);
    break;

    /*
    * Method POST
    * Sample Request: 
    *{
    *      "id": "2",
    *      "name": "samsung3",
    *      "description": "This is a nice description",
    *      "size": "XL",
    *      "cost": "240.00"
    *  }
    *
    * Sample Response:
    * {
    *      "id": 1 <inserted id>
    *  }
    * 
    */
    case 'POST':
      $result = Api::post($jsonRequest, $headers);
    break;

    /*
    * Method PUT
    * Sample Request: 
    * {
    *      "id": "1",
    *      "name": "test",
    *      "description": "This an edited description",
    *  }
    *
    * Sample Response:

    * {
    *      "id": "1",
    *      "name": "test",
    *      "description": "This an edited description",
    *      "size": "XL",
    *      "cost": "240.00"
    *  }
    * 
    */
    case 'PUT': 
      $result = Api::put($jsonRequest, $headers);
    break;

    /*
    * Method DELETE
    * Sample Request: 
    * {
    *      "id": 1
    *  }
    *
    * Sample Response:

    * {
    *      "message": "Item deleted"
    *  }
    * 
    */
    case 'DELETE':
      $result = Api::delete($jsonRequest, $headers);
    break;
    default:
      exit("Method not supported");
    
  }

  echo json_encode($result);

}