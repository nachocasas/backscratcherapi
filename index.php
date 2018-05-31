<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

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
    if(!Auth::isAuth($headers['Authorization'])){
      return array('error' => 'Invalid Token');
    }
    $result = ScratcherService::getAll();
    return $result;
  }
  
  public static function post($data, $headers){
    if(!Auth::isAuth($headers['Authorization'])){
      return array('error' => 'Invalid Token');
    }
    $validate = self::validate($data, 'post');
    $result = $validate === true ? ScratcherService::add($data) : array('error' => $validate);

    return $result;
  }

  public static function put($data, $headers){
    if(!Auth::isAuth($headers['Authorization'])){
      return array('error' => 'Invalid Token');
    }
    $validate = self::validate($data, 'put');
    $result = $validate === true ? ScratcherService::update($data) : array('error' => $validate);
    return $result;
  }

  public static function delete($data, $headers){
    if(!Auth::isAuth($headers['Authorization'])){
      return array('error' => 'Invalid Token');
    }
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

  $headers = apache_request_headers();

  print_r($headers);
  if(!$headers['Authorization']){
    echo json_encode(array('error' => 'Auth header missing'));
    exit();
  }

  if(!Auth::isAuth($headers['Authorization'])){
    echo json_encode(array('error' => 'Invalid Token'));
    exit();
  }

  switch($_SERVER['REQUEST_METHOD']){
    case 'GET':
      $result = Api::get($headers);
    break;
    case 'POST':
      $result = Api::post($jsonRequest, $headers);
    break;
    case 'PUT': 
      $result = Api::put($jsonRequest, $headers);
    break;
    case 'DELETE':
      $result = Api::delete($jsonRequest, $headers);
    break;
    default:
      exit("Method not supported");
    
  }

  echo json_encode($result);

}