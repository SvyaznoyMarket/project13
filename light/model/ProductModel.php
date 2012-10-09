<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 31.05.12
 * Time: 18:42
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('viewPath').'dataObject/product/ProductShortData.php');
require_once(Config::get('viewPath').'dataObject/product/ProductData.php');
require_once(Config::get('rootPath').'/lib/log4php/Logger.php');
//require_once(Config::get('viewPath').'dataObject/PromoData.php');

class ProductModel
{

  /**
   * @param int[] $idList
   * @param bool $loadDynamic
   * @return ProductData[]
   */
  public function getProductsByIdList($idList, $loadDynamic = true){
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);

    if (count($ids) < 1){
      return array();
    }

    TimeDebug::start('ProductModel:getProductsByIdList:clientV2');

    $data = array();
    $callback = function($response) use (&$data)
    {
      if (empty($data))
        $data = $response;
      else // array_merge do not combine equals keys
        foreach ($response as $key => $value)
          $data[$key] = array_merge($data[$key], $value);
    };


    App::getCoreV2()->addQuery('product/get-static', array('id' => $ids, 'geo_id' => App::getCurrentUser()->getRegion()->getId()), array(), $callback);
    if ($loadDynamic)
      App::getCoreV2()->addQuery('product/get-dynamic', array('id' => $ids, 'geo_id' => App::getCurrentUser()->getRegion()->getId()), array(), $callback);
    App::getCoreV2()->execute();
    $list = array();
    foreach ($data as $item)
      $list[] = $this->create($item);

    TimeDebug::end('ProductModel:getProductsByIdList:clientV2');

    return $list;
  }

  /**
   * @param string[] $tokenList
   * @param bool $loadDynamic
   * @return ProductData[]
   */
  public function getProductsByTokenList($tokenList, $loadDynamic = true){
    $tokens = array();
    foreach($tokenList as $token){
      $tokens[] = $token;
    }
    $tokens = array_unique($tokens);

    if (count($tokens) < 1){
      return array();
    }

    TimeDebug::start('ProductModel:getProductsByIdList:clientV2');

    $data = array();
    $callback = function($response) use (&$data)
    {
      if (empty($data))
        $data = $response;
      else // array_merge do not combine equals keys
        foreach ($response as $key => $value)
          $data[$key] = array_merge($data[$key], $value);
    };


    App::getCoreV2()->addQuery('product/get-static', array('slug' => $tokens, 'geo_id' => App::getCurrentUser()->getRegion()->getId()), array(), $callback);
    if ($loadDynamic)
      App::getCoreV2()->addQuery('product/get-dynamic', array('slug' => $tokens, 'geo_id' => App::getCurrentUser()->getRegion()->getId()), array(), $callback);
    App::getCoreV2()->execute();
    $list = array();
    foreach ($data as $item)
      $list[] = $this->create($item);

    TimeDebug::end('ProductModel:getProductsByIdList:clientV2');

    return $list;
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
   * @param int[] $idList
   * @param $callback
   * @return ProductShortData[]
   */
  public function getProductsShortDataByIdListAsync($idList, $callback){
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
    $self = $this;
    $count = 2;
    $cb = function($response) use (&$self, &$data, &$callback, &$count)
    {
      /** @var $self ProductModel */
      if (empty($data))
        $data = $response;
      else // array_merge do not combine equals keys
        foreach ($response as $key => $value)
          $data[$key] = array_merge($data[$key], $value);
      $count--;
      if($count === 0)
      {
        $list = array();
        foreach ($data as $item)
          $list[] = new ProductShortData($item);
        $callback($list);
      }
    };
    App::getCoreV2()->addQuery('product/get-static', array('id' => $ids, 'geo_id' => App::getCurrentUser()->getRegion()->getId()), array(), $cb);
    App::getCoreV2()->addQuery('product/get-dynamic', array('id' => $ids, 'geo_id' => App::getCurrentUser()->getRegion()->getId()), array(), $cb);
  }

  public function create($data){
    $product = new ProductData($data);
    return $product;
  }

}
