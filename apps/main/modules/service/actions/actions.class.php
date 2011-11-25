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
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    if (!isset($request['serviceCategory']) || !$request['serviceCategory']){
        //главная страница f1
        $serviceCategory = ServiceCategoryTable::getInstance()->getQueryObject()->where('core_parent_id IS NULL')->fetchOne();
        $list = ServiceCategoryTable::getInstance()
                        ->createQuery('sc')
                        ->innerJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
                        ->where('sc.core_parent_id=?',$serviceCategory['core_id'])->fetchArray();
    } else {
        //страница категории
        $serviceCategory = $this->getRoute()->getObject();
        #echo get_class($serviceCategory);
        $list = ServiceCategoryTable::getInstance()
                        ->createQuery('sc')
                        ->innerJoin('sc.ServiceRelation as rel on sc.id=rel.category_id')
                        ->orderBy('sc.lft')->fetchArray();
        //если первый уровень - выбираем перую подкатегорию и переходим на неё
        if ($serviceCategory['level'] == 1){
            $getNext = false;
            foreach($list as $item){
                if ($getNext){
                    $newCatId = $item['id'];
                    break;
                }
                if ($item['id'] == $serviceCategory['id']) $getNext = true;
            }
            $serviceCategory = ServiceCategoryTable::getInstance()->getById($newCatId);
        }
        
        $showFlag = false;
        foreach($list as $item){
            if ($item['id'] == $serviceCategory['id']){
                $showFlag = true;
            }
            if ($showFlag){
                if ($serviceCategory['id'] != $item['id'] && $serviceCategory['level'] == $item['level']){
                    $showFlag = false;
                    break;
                }   
                $listInner[] = $item;
                $listInnerCatId[] = $item['id'];
            }
        }
        
        $priceList = ProductPriceListTable::getInstance()->getCurrent();
        $priceListDefault = ProductPriceListTable::getInstance()->getDefault();
        #echo $priceListId->id .'----$priceListId';
        //получаем списки сервисов
        $serviceList = ServiceTable::getInstance()
                        ->createQuery('s')
                        ->distinct()
                        ->leftJoin('s.ServiceCategoryRelation sc on s.id=sc.service_id ')
                        ->leftJoin('s.Price p on s.id=p.service_id ')
                        #->addWhere('p.service_price_list_id = ? ', array($priceListDefaultId->id) )
                        ->addWhere('sc.category_id IN ('.implode(',', $listInnerCatId). ')' )->fetchArray();
        foreach($serviceList as & $service) {
            foreach($service['Price'] as $price) {
                if ($priceList->id == $price['service_price_list_id']) {
                  $service['currentPrice'] = $price['price'];
                  break;
                }
            }
            //если для текущего региона цены нет, ищем цену для региона по умолчанию
            if (!isset($service['currentPrice']) && $priceList->id != $priceListDefault->id ) {
              foreach($service['Price'] as $price) {
                  if ($priceListDefault->id == $price['service_price_list_id']) {
                      $service['currentPrice'] = $price['price'];
                      break;
                  }
              }          
            }            
        }
        #print_r($serviceList);
        #$list = $serviceCategory->getServiceList( array('level') );
    }
    $this->getResponse()->setTitle('F1 - '.$serviceCategory['name'].' – Enter.ru');

    
    $this->setVar('serviceCategory', $serviceCategory, true);
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
    $this->service = $this->getRoute()->getObject();
    $this->getResponse()->setTitle('F1 - '.$this->service->name.' – Enter.ru');
    
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
