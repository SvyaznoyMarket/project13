<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 20.04.12
 * Time: 11:43
 * To change this template use File | Settings | File Templates.
 */
class error
{
  public function jsonErrorMessage($params, Response $response){
    $return = array(
      'success' => false,
      'error' => $params['message']
    );
    $response->setContentType('application/json');
    $response->setContent(json_encode($return));
  }
}
