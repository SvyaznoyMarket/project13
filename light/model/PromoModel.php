<?php
namespace light;

use InvalidArgumentException;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.04.12
 * Time: 14:49
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('viewPath').'dataObject/PromoData.php');
//require_once(Config::get('helperPath').'DateFormatter.php');

class PromoModel
{
  private $imageSizes = array(
    "230x302",
    "768x302",
    "920x320"
  );
//  const IMG_EXCLUSIVE_PATH = "230x302";
//  const IMG_BIG_PATH       = "768x302";
//  const IMG_SMALL_PATH     = "920x320";

  /**
   * @return PromoData[]
   */
  public function getActivePromo(){
    TimeDebug::start('PromoModel:getActivePromo:clientV1');
    $data = App::getCoreV1()->query('promo.get', array(
      'count'   => false,
      'expand'  => array("item_list"),
      'limit'   => '',
      'start'   => 0
    ), array());
    TimeDebug::end('PromoModel:getActivePromo:clientV1');

    $return = array();

    foreach($data as $promo){
      if(!(bool) $promo['is_active']){
        continue;
      }

      $promoData = new PromoData();
      $promoData->setId((int) $promo['id']);
      $promoData->setName($promo['name']);
      $promoData->setTypeId((int) $promo['type_id']);
      $promoData->setUrl($promo['url']);
      $promoData->setImg($promo['media_image']);
      $promoData->setPosition((int) $promo['position']);
      $promoData->setStart($promo['start']);
      $promoData->setFinish($promo['finish']);
      $promoData->setItemList($promo['item_list']);
      $return[] = $promoData;
    }

    return $return;
  }

  /**
   * @param string $imgName
   * @param int $sizeType
   * @throws InvalidArgumentException
   * @return string
   */
  public function getImgUrl($imgName, $sizeType){

    if(!array_key_exists($sizeType, $this->imageSizes)){
      throw new InvalidArgumentException('unknown banner size type: '.$sizeType);
    }

    return Config::get('bannerImageUrl').$this->imageSizes[$sizeType].'/'.$imgName;
  }
}
