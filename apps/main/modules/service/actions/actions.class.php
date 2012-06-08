<?php

/**
 * service actions.
 *
 * @package    enter
 * @subpackage service
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceActions extends myActions
{
  public function preExecute()
  {
    parent::preExecute();

    $this->getRequest()->setParameter('_template', 'service');
  }

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    if (!isset($request['serviceCategory']) || !$request['serviceCategory']) {
      //главная страница f1
      $list = array();
      $serviceCategory = ServiceCategoryTable::getInstance()->getQueryObject()->where('core_parent_id IS NULL')->fetchOne();
      $listTop = ServiceCategoryTable::getInstance()
        ->createQuery('sc')
      //->innerJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
        ->where('sc.core_parent_id=?', $serviceCategory['core_id'])->fetchArray();
      foreach ($listTop as $topCat) {
        $listInner = ServiceCategoryTable::getInstance()
          ->createQuery('sc')
          ->where('sc.lft >= ? AND sc.rgt <= ?', array($topCat['lft'], $topCat['rgt']))
          ->innerJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
          ->fetchArray();
        //myDebug::dump($list);
        if (count($listInner)) {
          $list[] = $topCat;
        }

      }
      // print_r($list);
    } else {
      //страница категории
      $serviceCategory = $this->getRoute()->getObject();
      #echo get_class($serviceCategory);
      $list = ServiceCategoryTable::getInstance()
        ->createQuery('sc')
        ->leftJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
        ->orderBy('sc.lft')->fetchArray();
      //если первый уровень - выбираем перую подкатегорию и переходим на неё
      if ($serviceCategory['level'] == 1) {
        $getNext = false;
        foreach ($list as $key => $item) {
          if ($getNext) {
            if ($item['level'] == 2) {
              $currentLevel2 = $item;
            } elseif ($item['level'] == 3) {
              if (count($item['ServiceRelation'])) {
                $newCatId = $currentLevel2['id'];
                break;
              }
            }
          }
          if ($item['id'] == $serviceCategory['id']) $getNext = true;
        }
        $serviceCategory = ServiceCategoryTable::getInstance()->getById($newCatId);
      }

      $showFlag = false;
      foreach ($list as $item) {
        if ($item['id'] == $serviceCategory['id']) {
          $showFlag = true;
        }
        if ($showFlag) {
          if ($serviceCategory['id'] != $item['id'] && $serviceCategory['level'] == $item['level']) {
            $showFlag = false;
            break;
          }
          $listInner[] = $item;
          $listInnerCatId[] = $item['id'];
        }
      }

      //$priceListId = $this->getUser()->getRegion()->product_price_list_id;
      //echo $priceListId .'----$priceListId';
      $region = $this->getUser()->getRegion();
      $priceListId = $region['product_price_list_id'];
      //получаем списки сервисов
      $serviceList = ServiceTable::getInstance()
        ->createBaseQuery()
        ->distinct()
        ->innerJoin('service.ServiceCategoryRelation sc WITH sc.category_id IN (' . implode(',', $listInnerCatId) . ')')
        ->leftJoin('service.Price p WITH p.service_price_list_id = ? AND product_id IS NULL', $priceListId)
        ->orderBy('service.name ASC')
        ->execute();
    }
    $this->getResponse()->setTitle('F1 - ' . $serviceCategory['name'] . ' – Enter.ru');


    $this->setVar('serviceCategory', $serviceCategory, true);
    #myDebug::dump($list);
    $this->setVar('list', $list, true);
    if (isset($listInner)) $this->setVar('listInner', $listInner, true);
    if (isset($serviceList)) $this->setVar('serviceList', $serviceList, true);
  }

  /**
   * Executes show action
   *
   * @param sfRequest $request A request object
   */
  public function executeShow(sfWebRequest $request)
  {
    if (!isset($request['service']) || !$request['service']) {
      return;
    }

    /**
     * первод из токена ядра в токен сайта
     */

    $service = RepositoryManager::getService()->getByToken($request['service']);

    $params = array(
      'price' => true,
      'price_product' => 0
    );

    if($service){
      $this->service = ServiceTable::getInstance()->createBaseQuery($params)->where('core_id = ?', $service->getId())->fetchOne();
    }
    else{
      $this->service = ServiceTable::getInstance()->createBaseQuery($params)->where('token = ?', $request['service'])->fetchOne();
    }

    $this->forward404If(!$this->service);

    $this->getResponse()->setTitle('F1 - ' . $this->service->name . ' – Enter.ru');

  }

  /**
   * Executes category action
   *
   * @param sfRequest $request A request object
   */
  public function executeCategory(sfWebRequest $request)
  {
    $list = ServiceCategoryTable::getInstance()->getList();

    $this->setVar('list', $list, true);
  }


}
