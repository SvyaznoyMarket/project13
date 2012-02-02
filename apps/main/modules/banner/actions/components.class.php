<?php

/**
 * banner components.
 *
 * @package    enter
 * @subpackage banner
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bannerComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param Slot $slot Слот
  */
  public function executeShow()
  {
    /*
    if (!$this->slot instanceof Slot)
    {
      $this->slot = SlotTable::getInstance()->getDefaultRecord();
    }
    $this->view = preg_replace('/^banner_/', '', $this->slot->token);
    */

    $this->view = !empty($this->view) ? $this->view : 'default';

    $list = array();
    //foreach (BannerTable::getInstance()->getListBySlot($this->slot) as $banner)
    foreach (BannerTable::getInstance()->getList(array('hydrate_array' => true)) as $banner)
    {
      $productList = array();
      foreach ($banner['Item'] as $bannerItem)
      {
        if (!empty($bannerItem['Object']))
        {
          $productList[] = $bannerItem['Object'];
        }
      }

      $link = false;
      if (!count($productList) && !$banner['is_dummy'])
      {
        continue;
      }
      else if (1 == count($productList))
      {
        $link = $this->generateUrl('productCard', array('product' => $productList[0]['token_prefix'].'/'.$productList[0]['token']), true);
      }
      elseif (count($productList) > 1) {
        $link = $this->generateUrl('product_set', array('products' => implode(',', array_map(function($i) { return $i['barcode']; }, $productList))), true);
      }
      else
      {
        $link = "#";
      }

      //myDebug::dump($banner);
      $item = array(
        'alt'  => $banner['name'],
        'imgs' => BannerTable::getInstance()->getImageUrl($banner, 0),
        'imgb' => BannerTable::getInstance()->getImageUrl($banner, 1),
        'url'  => $link,
        't'    =>
          !empty($banner['timeout'])
          ? $banner['timeout']
          : (count($list) ? sfConfig::get('app_banner_timeout', 6000) : 10000)
        ,
        'ga'  => $banner['id'] . ' - ' . $banner['name']
      );
      if (empty($item['imgs']) || empty($item['imgb'])) continue;

      $list[] = $item;
    }

    $this->setVar('list', $list, true);
  }
}
