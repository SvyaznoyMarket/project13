<?php

/**
 * search actions.
 *
 * @package    enter
 * @subpackage search
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class searchActions extends myActions
{
  private $_validateResult;

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {

    $limit = sfConfig::get('app_product_max_items_on_category', 20);
	  $page = $request->getParameter('page', 1);
    $offset = intval($page - 1) * $limit;
    $this->forward404If($offset < 0, 'Неверный номер страницы');

    //myDebug::dump($request, 1);
    //$this->searchString = iconv('windows-1251', 'utf-8', $request['q']);
    $this->searchString = $request->getParameter('q');
    $this->forward404Unless($this->searchString);

    $title = 'Вы искали “'.htmlspecialchars($this->searchString).'”';
    if ($page)
    {
      $title .= ' – '.$page;
    }
    $this->getResponse()->setTitle($title.' – Enter.ru');

    $this->productType = !empty($request['product_type']) ? ProductTypeTable::getInstance()->find($request['product_type']) : false;

    // запрос к core
    $params = array(
      'request'         => $this->searchString,
      'start'           => $offset,
      'limit'           => $limit,
      'type_id'         => $this->getCoreIdBySearchType('product'), // ищет только товары
      'product_type_id' => $this->productType ? array($this->productType->core_id) : array(),
      'is_product_type_first_only' => $this->productType ? 'false' : 'true',
    );
    $response = Core::getInstance()->query('search.get', $params);
    #myDebug::dump($response);
    if (!$response)
    {
      return sfView::ERROR;
    }
    else if (isset($response['result']) && ('empty' == $response['result']))
    {
      $this->setTemplate('empty');

      return sfView::SUCCESS;
    }

    if (!$this->productType)
    {
      $this->productType = !empty($response[1]['type_list'][0]['type_id']) ? ProductTypeTable::getInstance()->getByCoreId($response[1]['type_list'][0]['type_id']) : false;
    }

    if ($request->isXmlHttpRequest())
    {
      $empty = true;

      if (is_array($response)) foreach ($response as $core_id => $data)
      {
        if ($data['count'] > 0)
        {
          $empty = false;
          break;
        }
      }

      return $this->renderJson(array(
        'success' => !$empty,
        'data'    => array(
          'content' => $empty ? $this->getPartial($this->getModuleName().'/popup', array(
            'count'        => 0,
            'searchString' => $this->searchString,
          )) : null
        ),
      ));
    }

    $productTypeList = array();
    $pagers = array();
    if (is_array($response)) foreach ($response as $core_id => $data)
    {
      $type = $this->getSearchTypes($core_id);
      if (null == $type) continue;

      if (('product' == $type) && !empty($data['type_list']))
      {
        $coreIds = array();
        foreach ($data['type_list'] as $productTypeData)
        {
          $coreIds[$productTypeData['type_id']] = $productTypeData['count'];
        }

        $productTypeList = ProductTypeTable::getInstance()->getListByCoreIds(array_keys($coreIds), array('order' => '_index'));
        foreach ($productTypeList as $productType)
        {
          $productType->mapValue('_product_count', $coreIds[$productType->core_id]);

          if ($productType->id == $this->productType->id)
          {
            $this->productType->mapValue('_product_count', $productType->_product_count);
          }
        }
      }

      $pagers[$type] = call_user_func_array(array($this, 'get'.ucfirst($type).'Pager'), array($data));
    }

    $this->setVar('searchString', $this->searchString, false);
    $this->setVar('pagers', $pagers, true);
    $this->setVar('productTypeList', $productTypeList, true);
    $this->setVar('resultCount', $response[1]['count'], true);
  }

  public function executeAjax(sfWebRequest $request)
  {
    //проверим сам запрос
    $this->searchString = $request['q'];
    if (!$this->searchString) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Не получен поисковый запрос.';
      return $this->_refuse();
    }

    //проверим страницы и количество на них
    if (isset($request['num'])) $limit = $request['num'];
    else $limit = sfConfig::get('app_product_max_items_on_category', 20);
	$page = $request->getParameter('page', 1);
    $offset = intval($page - 1) * $limit;
    if ($offset < 0) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Неверный номер страницы.';
      return $this->_refuse();
    }

    //тип товаров, если есть
    $this->productType = !empty($request['product_type']) ? ProductTypeTable::getInstance()->find($request['product_type']) : false;


    // запрос к core
    $params = array(
      'request'         => $this->searchString,
      'start'           => $offset,
      'limit'           => $limit,
      'type_id'         => $this->getCoreIdBySearchType('product'), // ищет только товары
      'product_type_id' => $this->productType ? array($this->productType->core_id) : array(),
      'is_product_type_first_only' => $this->productType ? 'false' : 'true',
    );
    $response = Core::getInstance()->query('search.get', $params);
    //myDebug::dump($response);
    if (!$response) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Ошбика. Не удалось получить результаты поиска.';
      return $this->_refuse();
    } else if (isset($response['result']) && ('empty' == $response['result'])) {
      $this->setTemplate('emptyAjax');

      #return sfView::SUCCESS;
    }

    if (!$this->productType)
    {
      $this->productType = !empty($response[1]['type_list'][0]['type_id']) ? ProductTypeTable::getInstance()->getByCoreId($response[1]['type_list'][0]['type_id']) : false;
    }


    $this->setVar('searchString', $this->searchString, false);
    if (isset($response[1]) && isset($response[1]['count'])) {
        $this->setVar('resultCount', $response[1]['count'], true);
    }

    $productTypeList = array();
    $pagers = array();
    if (is_array($response)) foreach ($response as $core_id => $data)
    {
      $type = $this->getSearchTypes($core_id);
      if (null == $type) continue;

      if (('product' == $type) && !empty($data['type_list']))
      {
        $coreIds = array();
        foreach ($data['type_list'] as $productTypeData)
        {
          $coreIds[$productTypeData['type_id']] = $productTypeData['count'];
        }

        $productTypeList = ProductTypeTable::getInstance()->getListByCoreIds(array_keys($coreIds), array('order' => '_index'));
        foreach ($productTypeList as $productType)
        {
          $productType->mapValue('_product_count', $coreIds[$productType->core_id]);

          if ($productType->id == $this->productType->id)
          {
            $this->productType->mapValue('_product_count', $productType->_product_count);
          }
        }
      }

      $pagers[$type] = call_user_func_array(array($this, 'get'.ucfirst($type).'Pager'), array($data));
    }

    $this->setVar('searchString', $this->searchString, false);
    $this->setVar('pagers', $pagers, true);
  }




  protected function getSearchTypes($core_id = null)
  {
    $types = array(
      1 => 'product',
      2 => 'news',
    );

    return null == $core_id ? $types : (isset($types[$core_id]) ? $types[$core_id] : null);
  }

  protected function getCoreIdBySearchType($type)
  {
    $return = null;

    foreach ($this->getSearchTypes() as $core_id => $searchType)
    {
      if ($searchType == $type)
      {
        $return = $core_id;
        break;
      }
    }

    return $return;
  }

  protected function getProductPager(array $data)
  {
    $view = $this->getRequestParameter('view');

    $list = !empty($data['data'])
      ? ProductTable::getInstance()->getListByCoreIds($data['data'], array(
        'property_view'   => 'expanded' == $view ? 'list' : false,
        'with_properties' => 'expanded' == $view ? true : false,
        'order'           => '_index',
        'with_model'      => true,
      ))
      : array()
    ;

    $pager = $this->_getPager($list, isset($this->productType->_product_count) ? $this->productType->_product_count : 0, sfConfig::get('app_product_max_items_on_category', 20), array());

    return $pager;
  }

  protected function getNewsPager(array $data)
  {
    return array();
  }

  public function _getPager($list, $count, $limit, array $params = array())
  {
    $page = (int)$this->getRequest()->getParameter('page', 1);

    $pager = new FilledPager($list, $count, $limit);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  private function _refuse(){
    return $this->renderJson(array(
      'success' => $this->_validateResult['success'],
      'data'    => array(
        'error' => $this->_validateResult['error'],
      ),
    ));
  }
}
