<?php

/**
 * service components.
 *
 * @package    enter
 * @subpackage order
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceSoaComponents extends myComponents
{

  /**
   * Executes listByProduct component
   *
   * @param product $product Продукт
   */
  public function executeListByProduct()
  {
    $list = $this->product->service;

    $servList = $this->getUser()->getCart()->getServices();
    $servListId = array();
    foreach ($servList as $serviceId => $next)
    {
      if(array_key_exists($this->product->id, $next)){
        $servListId[] = $serviceId;
      }
    }
    $this->setVar('servListId', $servListId, true);
    $this->setVar('list', $list, true);
  }

  public function executeList_for_product_in_cart()
  {
    if(array_key_exists('fullObject', $this->product)){
      $product = $this->product['fullObject'];
    }
    else{
      $productList = RepositoryManager::getProduct()->getListById(array($this->product['core_id']), true);
      /** @var $product ProductEntity */
      $product = reset($productList);
    }

    $result = array();
    $selectedNum = 0;
    foreach ($product->getServiceList() as $service)
    {
      $token = $service->getSiteToken();
      if (!$token) {
          continue;
      }
      $sel = false;
      foreach ($this->services as $selected)
      {
        if ($service->getId() == $selected['core_id'])
        {
          $selInfo = $selected;
          $sel = true;
          break;
        }
      }
      if ($sel)
      {
        $selectedNum++;
        $selInfo['selected'] = true;
        $selInfo['site_token'] = $token;
        $selInfo['total'] = $selInfo['quantity'] * $service->getPrice();
        $selInfo['totalFormatted'] = number_format($selInfo['quantity'] * $service->getPrice(), 0, ',', ' ');
        $selInfo['price'] = $service->getPrice();
        $selInfo['only_inshop'] = $service->getOnlyInShop();
        $selInfo['in_sale'] = $service->isInSale();
        $selInfo['priceFormatted'] = number_format($service->getPrice());
        $result[] = $selInfo;
      }
      else
      {
        $result[] = array(
          'selected' => false,
          'site_token' => $token,
          'name' => $service->getName(),
          'id' => $service->getId(),
          'token' => $service->getToken(),
          'price' => $service->getPrice(),
          'only_inshop' => $service->getOnlyInShop(),
          'in_sale' => $service->isInSale(),
          'priceFormatted' => number_format($service->getPrice()),
        );
      }
    }
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
    $serviceData['name'] = $this->service->name;
    $serviceData['description'] = $this->service->description;
    $serviceData['work'] = $this->service->work;
    $serviceData['main_photo'] = $this->service->getPhotoUrl();
    $this->setVar('service', $serviceData);
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
        'name' => $service->name,
        'photo' => $service->getPhotoUrl(2),
        'price' => $service->getCurrentPrice(),
        'priceFormatted' => $service->getFormattedPrice()
      );
    }
    $this->setVar('list', $serviceList);
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

    if (isset($this->serviceCategory) && $this->serviceCategory)
    {
      $parentCategory = $this->serviceCategory->getParentCategory();
      if (isset($parentCategory) && isset($parentCategory['name']))
      {
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
      if (isset($parentCategory) && isset($parentCategory['name']))
      {
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

