<?php

/**
 * shop components.
 *
 * @package    enter
 * @subpackage shop
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class shopComponents extends myComponents
{
/**
  * Executes navigation component
  *
  * @param array $region Регион array('name' => '', 'type' => '')
  * @param Shop   $shop   Магазин
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => 'Магазины Enter в '.(RepositoryManager::getRegion()->getLinguisticCase($this->region['name'], 'п') ? mb_ucfirst(RepositoryManager::getRegion()->getLinguisticCase($this->region['name'], 'п')) : ((($this->region['type'] == 'city')? 'г.':'').$this->region['name'])),
      'url'  => $this->generateUrl('shop', array('region' => $this->region->token)),
    );
    if (isset($this->shop))
    {
      $list[] = array(
        'name' => (string)$this->shop,
        'url'  => $this->generateUrl('shop_show', $this->shop),
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes show component
  *
  * @param Shop $shop Магазин
  */
  public function executeShow()
  {
    if (!in_array($this->view, array('default', 'inlist')))
    {
      $this->view = 'default';
    }

    $item = array(
      'id'                => $this->shop->id,
      'name'              => (string)$this->shop,
      'address'           => $this->shop->address,
      'phonenumbers'      => $this->shop->phonenumbers,
      'regime'            => $this->shop->regime,
      'description'       => $this->shop->description,
      'way_walk'          => $this->shop->way_walk,
      'way_auto'          => $this->shop->way_auto,
      'latitude'          => $this->shop->latitude,
      'longitude'         => $this->shop->longitude,
      'is_reconstruction' => $this->shop->is_reconstruction,
      'photos'       => array(),
      'panorama'     =>
        !empty($this->shop->panorama)
        ? array('swf' => "/panoramas/shops/{$this->shop->core_id}/tour.swf", 'xml' => "/panoramas/shops/{$this->shop->core_id}/tour.xml")
        : false
      ,
    );


    foreach ($this->shop->Photo as $i => $shopPhoto)
    {
      $item['photos'][] = array(
        'url_small'   => $shopPhoto->getPhotoUrl(5), // 1
        'url_big'     => $shopPhoto->getPhotoUrl(5), // 4
      );
    }

    if ('inlist' == $this->view)
    {
      $item['url'] = $this->generateUrl('shop_show', $this->shop);
      $item['main_photo'] = isset($this->shop->Photo[0]) ? array(
        'url_small' => $this->shop->Photo[0]->getPhotoUrl(5),
      ) : false;
    }

    // Clones first photo if shop has panorama
    if (count($item['photos']) && !empty($this->shop->panorama))
    {
      array_unshift($item['photos'], $item['photos'][0]);
    }

    $this->setVar('item', $item, true);
  }
 /**
  * Executes list component
  *
  * @param Region $region Регион
  */
  public function executeList()
  {
    $shopList = ShopTable::getInstance()->getListByRegion($this->region['id'], array('is_reconstruction' => true, ));
    if (!count($shopList)) {
      return sfView::NONE;
    }

    $this->setVar('shopList', $shopList, true);
  }

 /**
  * Executes map component
  *
  * @param myDoctrineCollection $shopList Коллекция магазинов
  */
  public function executeMap()
  {
    $regionList = RepositoryManager::getRegion()->getShopAvailable();

    $markers = array();
    /*$regions = array();
    foreach ($regionList as $region)
    {
      $regions[] = array(
        'id'        => $region->getId(),
        'token'     => $region->getToken(),
        'latitude'  => $region->getLatitude(),
        'longitude' => $region->getLongitude(),
      );*/

      $Shop = ShopTable::getInstance()->getListByRegion($this->region['id'], array('is_reconstruction' => true, ));
      foreach ($Shop as $shop)
      {
        $markers[$shop->id] = array(
          'id'                => $shop->id,
          'region_id'         => $shop->region_id,
          'link'              => url_for( 'shop_show', array( 'sf_subject' => $shop)),
          'name'              => $shop->name,
          'address'           => $shop->address,
          'regtime'           => $shop->regime,
          'latitude'          => $shop->latitude,
          'longitude'         => $shop->longitude,
          'is_reconstruction' => $shop->is_reconstruction,
        );
      }
    //}

    $this->setVar('regionList', $regionList, true);
    $this->setVar('markers', $markers, true);
    //$this->setVar('regions', $regions, true);
  }

  function executeSeo_counters_advance() {

  }
}

