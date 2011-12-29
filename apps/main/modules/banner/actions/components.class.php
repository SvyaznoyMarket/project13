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
        if ($bannerItem['Object'])
        {
          $productList[] = $bannerItem['Object'];
        }
      }

      $link = false;
      if (!count($productList))
      {
        continue;
      }
      else if (1 == count($productList))
      {
        $link = url_for('productCard', array('product' => $productList[0]['token_prefix'].'/'.$productList[0]['token']), true);
      }
      else {
        $link = url_for('product_set', array('products' => implode(',', array_map(function($i) { return $i['barcode']; }, $productList))), true);
      }

      $list[] = array(
        'name'          => $banner['name'],
        'image_small'   => $banner['image'],
        'image_big'     => $banner['image'],
        'link'          => $link,
        'timeout'       => !empty($banner['timeout']) ? $banner['timeout'] : sfConfig::get('app_banner_timeout', 2000),
      );
    }

    $this->setVar('list', $list, true);
  }
}
