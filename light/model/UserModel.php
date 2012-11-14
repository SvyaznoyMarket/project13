<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 20.08.12
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('viewPath').'dataObject/UserData.php');

class UserModel
{

  /**
   * @param string $token
   * @return  UserData | null
   */
  public function getByAuthToken($token){
    try{
      $response = App::getCoreV2()->query('user/get', array('token' => $token), array());
      if(!is_array($response) || empty($response)){
        return null;
      }
      return new UserData($response);
    }
    catch(\Exception $e){
      return null;
    }
    return null;
  }

  /**
   * @param int $id
   * @return UserData|null
   */
  public function getById($id){
    try{
      $response = App::getCoreV1()->query('user.get', array('id' => (int)$id, 'expand' => array()), array());
      if(!is_array($response) || !isset($response[0]) || !is_array($response[0])){
        return null;
      }
      return new UserData($response[0]);
    }
    catch(\Exception $e){
      return null;
    }
    return null;
  }

  /**
   * @param $params
   * @return null| string
   */
  public function getAuthTokenByEmail($email, $password){
    $params = array(
      'email' => $email,
      'password' => $password
    );
    return $this->authorize($params);
  }

  /**
   * @param $params
   * @return null| string
   */
  public function getAuthTokenByMobile($mobile, $password){
    $params = array(
      'mobile' => $mobile,
      'password' => $password
    );
    return $this->authorize($params);
  }

  /**
   * @param $params
   * @return null| string
   */
  private function authorize($params){
    try{
      $response = App::getCoreV2()->query('user.auth', $params, array());
      if(!is_array($response) || empty($response) || !array_key_exists('token', $response)){
        return null;
      }
      return $response['token'];
    }
    catch(\Exception $e){
      return null;
    }
    return null;
  }
}
