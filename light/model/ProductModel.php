<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 31.05.12
 * Time: 18:42
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
//require_once(VIEW_PATH.'dataObject/PromoData.php');

class ProductModel
{

  /**
   * @param int[] $idList
   * @return ProductData[]
   */
  public function getProductsByIdList($idList){
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);

    TimeDebug::start('ProductModel:getProductsByIdList:clientV2');
    $data = App::getCoreV2()->query('product.get-static', array(
      'id' => $ids,
    ));
    TimeDebug::end('ProductModel:getProductsByIdList:clientV2');
//    return $data;
    return array();
  }

  /**
   * @param int[] $idList
   * @param string[] $valueNames
   * @return array [ProductId => [productPropertyName => ProductPropertyValue, ...], ...]
   */
  public function getProductPropertiesByIdList($idList, $valueNames){
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);
    if(count($ids) < 1){
      return array();
    }

    TimeDebug::start('ProductModel:getProductPropertiesByIdList:clientV2');
    $data = App::getCoreV2()->query('product.get-static', array('id' => $ids), array());
    TimeDebug::end('ProductModel:getProductPropertiesByIdList:clientV2');

    $valueNames = array_flip($valueNames);
    foreach($valueNames as $key => $val){
      $valueNames[$key] = Null;
    }

    $return = array();
    foreach ($data as $product){
      $return[$product['id']] = $valueNames;
      foreach($product as $propertyName => $propertyValue){
        if(array_key_exists($propertyName, $valueNames)){
          $return[$product['id']][$propertyName] = $propertyValue;
        }
      }
    }

    return $return;
  }

  /**
   * Метод создан для ускорения
   * @param int[] $idList
   * @param str[] $valueNames имена необходимых полей
   * @param $callback
   */
  public function getProductValuesByIdListAsync($idList, $valueNames, $callback){
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);

    if(count($ids) < 1){
      $callback(array());
      return;
    }

    $data = array();
    $cb = function($response) use (&$data, &$valueNames, &$callback)
    {
      $valueNames = array_flip($valueNames);
      foreach($valueNames as $key => $val){
        $valueNames[$key] = Null;
      }

      $return = array();
      foreach ($data as $product){
        $return[$product['id']] = $valueNames;
        foreach($product as $propertyName => $propertyValue){
          if(isset($valueNames[$propertyName])){
            $return[$product['id']][$propertyName] = $propertyValue;
          }
        }
      }
      $callback($return);
    };
    App::getCoreV2()->addQuery('product.get-static', array('id' => $ids), array(), $cb);
  }

}
