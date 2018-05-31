<?php 

require_once __DIR__.'/../config.php';

use \Firebase\JWT\JWT;

class Auth {

  public static function getToken($appId){
    global $config;
    $secretKey = base64_decode($config['Auth']['secret_key']);

    $token = array(
      'iat'  => time(),         // Issued at: time when the token was generated
      'jti'  => base64_encode(random_bytes (32)),          // Json Token Id: an unique identifier for the token
      'iss'  => "test.com",       // Issuer
      'nbf'  => time(),        // Not before
      'exp'  => time() + 380,           // Expire
      'data' => array(                  // Data related to the signer user
          'appId'   => 3, // userid from the users table
        )
      );

    $jwt = JWT::encode($token, $secretKey);

    return $jwt;
  }

  public static function isAuth($token){
    global $config;
    try {
      $secretKey = base64_decode($config['Auth']['secret_key']);
      $decToken = JWT::decode($token, $secretKey, array('HS256'));
      return property_exists($decToken, "data");
    } catch(Exception $e) {
      return false;
    }
  }
}