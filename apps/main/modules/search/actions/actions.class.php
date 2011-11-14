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

    $title = 'Вы искали “'.  htmlspecialchars($this->searchString).'”';
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
    //myDebug::dump($response);
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
          if ($productType->id == ($this->productType ? $this->productType->id : null))
          {
            $productType->mapValue('_selected', true);
            $this->productType->mapValue('_product_count', $coreIds[$productType->core_id]);
          }
          else {
            $productType->mapValue('_selected', false);
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
    $list = !empty($data['data'])
      ? ProductTable::getInstance()->getListByCoreIds($data['data'], array(
        'property_view'   => 'list',
        'with_properties' => 'expanded' == $this->getRequestParameter('view') ? true : false,
        'order'           => '_index',
      ))
      : array()
    ;

    $pager = $this->getPager($list, isset($this->productType->_product_count) ? $this->productType->_product_count : 0, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));

    return $pager;
  }

  protected function getNewsPager(array $data)
  {
    return array();
  }

  public function getPager($list, $count, array $params = array())
  {
    $params = myToolkit::arrayDeepMerge(array(
      'limit' => 20,
      'page'  => (int)$this->getRequest()->getParameter('page', 1),
    ), $params);

    $pager = new FilledPager($list, $count, $params['limit']);
    $pager->setPage($params['page']);
    $pager->init();

    return $pager;
  }
}
