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

      $serviceData['currentPrice'] = $this->service->getCurrentPrice();
      if (isset($service['currentPrice'])) {
          $service['currentPrice'] = number_format($serviceData['currentPrice'], 2, ',', ' ');
      }
      $serviceData['name'] = $this->service->name;
      $serviceData['description'] = $this->service->description;
      $serviceData['work'] = $this->service->work;
      $serviceData['main_photo'] = $this->service->getPhotoUrl();
      $this->setVar('service', $serviceData);
  }

  public function executeAlike_service()
  {
    $nearParent = ServiceCategoryTable::getInstance()
            ->createQuery('sc')
            ->innerJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
            ->where('rel.service_id = ? AND sc.level = ?', array($this->service->id, 3) )
            ->execute();
    $list = ServiceTable::getInstance()
            ->createQuery('s')
            ->innerJoin('s.CategoryRelation as rel on s.id=rel.service_id')
            ->where('rel.category_id = ?', $nearParent[0]->id )
            ->addWhere('s.id != ?', $this->service->id)
            ->execute();
    $this->setVar('list', $list);

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

