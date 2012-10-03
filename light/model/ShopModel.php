<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 12.05.12
 * Time: 16:31
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('viewPath').'dataObject/ShopData.php');

class ShopModel
{

  /**
   * @param $id
   * @return ShopData|null
   */
  public function getById($id){
    $id = (int) $id;
    $response = App::getCoreV2()->query('shop.get', array(), array('id' => array($id)));
    if (!isset($response[0]) || !is_array($response[0])){
      return null;
    }

    return new ShopData($response[0]);
  }

}
