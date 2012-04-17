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
    foreach ($servList as $next)
    {
      foreach ($next['products'] as $product => $qty)
      {
        if ($product == $this->product->id)
        {
          $servListId[] = $next['id'];
        }
      }
    }
    $this->setVar('servListId', $servListId, true);
    $this->setVar('list', $list, true);
  }

  public function executeList_for_product_in_cart()
  {
    $factory = new ProductFactory();
    $prodOb = $factory->createProductFromCore(array('id'=> $this->product['core_id']), false, false, true);
    $list = $prodOb[0]->service;
    $result = array();
    $selectedNum = 0;
    if (is_array($list) && count($list))
    foreach ($list as $next)
    {
      if (!isset($next['site_token'])) {
          continue;
      }
      $sel = false;
      foreach ($this->services as $selected)
      {
        if ($next['id'] == $selected['core_id'])
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
        $selInfo['site_token'] = $next['site_token'];
        $selInfo['total'] = $selInfo['quantity'] * $next['price'];
        $selInfo['totalFormatted'] = number_format($selInfo['quantity'] * $next['price'], 0, ',', ' ');
        $selInfo['price'] = $next['price'];
        $selInfo['only_inshop'] = $next['only_inshop'];
        $selInfo['in_sale'] = $next['in_sale'];
        $selInfo['priceFormatted'] = number_format($next['price']);
        $result[] = $selInfo;
      }
      else
      {
        $result[] = array(
          'selected' => false,
          'site_token' => $next['site_token'],
          'name' => $next['name'],
          'id' => $next['id'],
          'token' => $next['token'],
          'price' => $next['price'],
          'only_inshop' => $next['only_inshop'],
          'in_sale' => $next['in_sale'],
          'priceFormatted' => number_format($next['price']),
        );
      }
    }
//      print_r($result);
//      die();

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

