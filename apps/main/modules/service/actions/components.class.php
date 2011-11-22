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
    $this->setVar('item', array(
      'name'         => (string)$this->service,
      'description'  => $this->service->description,
      'price'        => (isset($this->service->Price)) ? $this->service->Price->getFirst()->price : "",
    ), true);
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
      'url'  => url_for('service_list'),
    );  
    
    if (isset($this->serviceCategory)) {
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

