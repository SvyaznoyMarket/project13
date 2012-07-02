<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 02.07.12
 * To change this template use File | Settings | File Templates.
 */

class creditController
{
  /**
   * @param Response $response
   * @param array $params
   */
  public function Set(Response $response, $params=array()){
      if (isset($_GET['is_credit']) && $_GET['is_credit']) {
          $_SESSION['credit'] = 1;
      } else {
          $_SESSION['credit'] = 0;
      }
      $result['success'] = true;
      echo json_encode($result);
      //echo 'ok!';
  }
}