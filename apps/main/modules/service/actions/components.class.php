<?php

/**
 * service components.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceComponents extends myComponents
{

  /**
   * Executes listByProduct component
   *
   * @param product $product Продукт
   */
  public function executeListByProduct()
  {
    $list = $this->product->getServiceList();

    $servList = $this->getUser()->getCart()->getServices();
    $servListId = array();
    foreach ($servList as $serviceId => $service)
    {

      if(array_key_exists($this->product->id, $service)){
        $servListId[] = $serviceId;
      }
    }
    $this->setVar('servListId', $servListId, true);
    $this->setVar('list', $list, true);
  }

  public function executeList_for_product_in_cart()
  {
    $list = $this->product->getServiceList();
    $result = array();
    $selectedNum = 0;
    # print_r( $this->services );
    foreach ($list as $next)
    {
      $sel = false;
      foreach ($this->services as $selected)
      {
        if ($next->id == $selected['id']) {
          $selInfo = $selected;
          $sel = true;
          break;
        }
      }
      if ($sel) {
        $selectedNum++;
        $selInfo['selected'] = true;
        $selInfo['total'] = $selInfo['quantity'] * $selInfo['price'];
        $selInfo['totalFormatted'] = number_format($selInfo['quantity'] * $selInfo['price'], 0, ',', ' ');
        $selInfo['priceFormatted'] = $next->getFormattedPrice($this->product->id);
        $result[] = $selInfo;
      }
      else
      {
        $result[] = array(
          'selected' => false,
          'name' => $next->name,
          'id' => $next->id,
          'token' => $next->token,
          'priceFormatted' => $next->getFormattedPrice($this->product->id)
        );
      }
    }
    #  print_r($result);
    $this->setVar('selectedNum', $selectedNum, true);
    $this->setVar('list', $result, true);
  }

  /**
   * Executes show component
   *
   * @param Service $service Услуга
   */
  public function executeShow()
  {

    $serviceData['price'] = $this->service->getCurrentPrice();
    $serviceData['priceFormatted'] = $this->service->getFormattedPrice();
    #if (isset($service['currentPrice'])) {
    #    $service['currentPrice'] = number_format($serviceData['currentPrice'], 2, ',', ' ');
    #}
    $serviceData['token'] = $this->service->token;
    $serviceData['core_id'] = $this->service->core_id;
    $serviceData['name'] = $this->service->name;
    $serviceData['description'] = $this->service->description;
    $serviceData['work'] = $this->service->work;
    $serviceData['main_photo'] = $this->service->getPhotoUrl();
    $serviceData['isInSale'] = $this->service->isInSale();
    $serviceData['isOnlyInShop'] = $this->service->isOnlyInShop();

    $this->setVar('service', $serviceData, true);
  }

  public function executeAlike_service()
  {
    $serviceList = array();
    $nearParent = ServiceCategoryTable::getInstance()
      ->createQuery('sc')
      ->innerJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
      ->where('rel.service_id = ? AND sc.level = ?', array($this->service->id, 3))
      ->execute();
    $list = ServiceTable::getInstance()
      ->createQuery('s')
      ->innerJoin('s.CategoryRelation as rel on s.id=rel.service_id')
      ->where('rel.category_id = ?', $nearParent[0]->id)
      ->addWhere('s.id != ?', $this->service->id)
      ->orderBy('s.name ASC')
      ->execute();
    foreach ($list as $service)
    {
      $serviceList[] = array(
        'name' => $service->name,
        'token' => $service->token,
        'core_id' => $service->core_id,
        'name' => $service->name,
        'photo' => $service->getPhotoUrl(2),
        'price' => $service->getCurrentPrice(),
        'priceFormatted' => $service->getFormattedPrice(),
        'isInSale' => $service->isInSale(),
        'isOnlyInShop' => $service->isOnlyInShop()

      );
    }
    $this->setVar('list', $serviceList, true);
  }

  public function executeRoot_page()
  {

  }

  public function executeLeft_menu()
  {

  }

  public function executeCurrent_category_tree()
  {

  }

  public function executeNavigation()
  {
    $list = array();
    $list[] = array(
      'name' => 'F1 Сервис',
      'url' => $this->generateUrl('service_list'),
    );

    //  myDebug::dump($this->serviceCategory);
    if (isset($this->serviceCategory) && $this->serviceCategory && $this->serviceCategory->core_parent_id) {
      $parentCategory = $this->serviceCategory->getParentCategory();
      if (isset($parentCategory) && isset($parentCategory['name'])) {
        $list[] = array(
          'name' => $parentCategory['name'],
          'url' => $this->generateUrl('service_list', array('serviceCategory' => $parentCategory['token'])),
        );
      }
      $list[] = array(
        'name' => $this->serviceCategory['name'],
        'url' => $this->generateUrl('service_list'),
      );
    }
    elseif (isset($this->service))
    {
      $parentCategory = $this->service->getCatalogParent();
      if (isset($parentCategory) && isset($parentCategory['name'])) {
        $list[] = array(
          'name' => $parentCategory['name'],
          'url' => $this->generateUrl('service_list', array('serviceCategory' => $parentCategory['token'])),
        );
      }
      $list[] = array(
        'name' => $this->service['name'],
      );
    }


    $this->setVar('list', $list);
  }

}

