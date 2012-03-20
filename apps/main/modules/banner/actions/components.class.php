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
      $bannerTable = BannerTable::getInstance();
      $productList = array();

      foreach ($banner['Item'] as $bannerItem)
      {
        if (!empty($bannerItem['Object']))
        {
          $objectList[] = $bannerItem['Object'];
        }
      }

      $link = false;
      if (!empty($banner['link']))
      {
        $link = $banner['link'];
      }
      else if (!count($objectList) && !$bannerTable->isDummy($banner))
      {
        continue;
      }
      else
      {
        $link = $this->getBannerUrl($objectList, $bannerItem['type']);
      }

      //myDebug::dump($banner);
      $item = array(
        'alt'   => $banner['name'],
        'imgs'  => $bannerTable->getImageUrl($banner, 0),
        'imgb'  => BannerTable::getInstance()->getImageUrl($banner, $bannerTable->isExclusive($banner) ? 2 : 1),
        'url'   => $link,
        't'     =>
          !empty($banner['timeout'])
          ? $banner['timeout']
          : (count($list) ? sfConfig::get('app_banner_timeout', 6000) : 10000)
        ,
        'ga'    => $banner['id'] . ' - ' . $banner['name'],
      );
      if ($bannerTable->isExclusive($banner))
      {
        $item['is_exclusive'] = true;
      }
      if (empty($item['imgs']) || empty($item['imgb'])) continue;

      $list[] = $item;
    }

    $this->setVar('list', $list, true);
  }

  protected function getBannerUrl($list, $type)
  {
    $link = '#';
    if (1 == count($list))
    {
      switch ($type)
      {
        case 'product':
          $link = $this->generateUrl('productCard', array('product' => $list[0]['token_prefix'].'/'.$list[0]['token']), true);
        break;
        case 'product_category':
          $link = $this->generateUrl('productCatalog_category', array('productCategory' => (!empty($list[0]['token_prefix']) ? ($list[0]['token_prefix'].'/') : '').$list[0]['token']), true);
        break;
      }
    }
    elseif (count($list) > 1)
    {
      $barcodeLast = array();
      foreach ($list as $item)
      {
        if (isset($item['barcode']))
        {
          $barcodeList[] = $item['barcode'];
        }
        if (count($barcodeList))
        {
          $link = $this->generateUrl('product_set', array('products' => implode(',', $barcodeList)), true);
        }
      }
    }

    return $link;
  }
}
