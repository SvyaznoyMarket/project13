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

    $this->setVar('list', $list, true);
  }

  /**
  * Executes show component
  *
  * @param Service $service Услуга
  */
  public function executeShow()
  {
      //ищем цену для текущего региона
      
      $priceList = ProductPriceListTable::getInstance()->getCurrent();      
      foreach($this->service->Price as $price) {
          if ($priceList->id == $price['service_price_list_id']) {
              $service['currentPrice'] = $price['price'];
              break;
          }
      }    
      //если для текущего региона цены нет, ищем цену для региона по умолчанию
      if (!isset($service['currentPrice'])) {
          $priceListDefault = ProductPriceListTable::getInstance()->getDefault();      
          if ($priceList->id != $priceListDefault->id) {
              foreach($this->service->Price as $price) {
                  if ($priceListDefault->id == $price['service_price_list_id']) {
                      $service['currentPrice'] = $price['price'];
                      break;
                  }
              } 
          }
      }
      $service['currentPrice'] = number_format($service['currentPrice'], 2, ',', ' ');
      $service['name'] = $this->service->name;
      $service['description'] = $this->service->description;
      $service['work'] = $this->service->work;
      $this->setVar('service', $service);
  }#
  
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
      'url'  => url_for('service_list'),
    );  
    
    if (isset($this->serviceCategory) && $this->serviceCategory) {        
        $parentCategory = $this->serviceCategory->getParentCategory();
        if (isset($parentCategory) && isset($parentCategory['name'])) {
            $list[] = array(
              'name' => $parentCategory['name'],
              'url'  => url_for('service_list', array('serviceCategory' => $parentCategory['token'])),
            );     
        }
        $list[] = array(
          'name' => $this->serviceCategory['name'],
          'url'  => url_for('service_list'),
        );  
    } elseif (isset($this->service)) {
        $parentCategory = $this->service->getCatalogParent();      
        if (isset($parentCategory) && isset($parentCategory['name'])) {
            $list[] = array(
              'name' => $parentCategory['name'],
              'url'  => url_for('service_list', array('serviceCategory' => $parentCategory['token'])),
            );     
        } 
        $list[] = array(
          'name' => $this->service['name'],
        );  
    }


    $this->setVar('list', $list);      
  }
}

