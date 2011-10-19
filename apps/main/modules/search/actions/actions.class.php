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
    $offset = intval($request->getParameter('page', 1) - 1) * $limit;
    $this->forward404If($offset < 0, 'Неверный номер страницы');

    //$this->searchString = iconv('windows-1251', 'utf-8', $request['q']);
    $this->searchString = $request['q'];
    if (empty($this->searchString))
    {
      return sfView::NONE;
    }

    $productTypeList = $this->getProductTypes($request);

    // запрос к core
    $params = array(
      'request'         => $this->searchString,
      'start'           => $offset,
      'limit'           => $limit,
      'type_id'         => $this->getCoreIdBySearchType('product'), // ищет только товары
      'product_type_id' => $productTypeList->toValueArray('core_id'),
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

    $categories = array();
    $pagers = array();
    if (is_array($response)) foreach ($response as $core_id => $data)
    {
      $type = $this->getSearchTypes($core_id);
      if (null == $type) continue;

      $categories[$type] = array();

      $pagers[$type] = call_user_func_array(array($this, 'get'.ucfirst($type).'Pager'), array($data));

      if (('product' == $type) && !empty($data['types']))
      {
        $selected = $productTypeList->toValueArray('id');
        $coreIds = array_keys($data['types']);
        foreach (ProductTypeTable::getInstance()->getListByCoreIds($coreIds) as $productType)
        {
          $categories[$type][] = array(
            'record'   => $productType,
            'count'    => $data['types'][$productType->core_id],
            'selected' => in_array($productType->id, $selected),
          );
        }
      }
    }

    $this->categories = $categories;
    $this->pagers = $pagers;
  }



  protected function getProductTypes($request)
  {
    $list = ProductTypeTable::getInstance()->createList();

    $ids = is_array($request['product_types']) ? $request['product_types'] : array();
    if (count($ids) > 0)
    {
      $list = ProductTypeTable::getInstance()->createListByIds($ids);
    }

    return $list;
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
      ? ProductTable::getInstance()->getListByCoreIds($data['data'], array('view' => 'list'))
      : array()
    ;

    $pager = $this->getPager($list, $data['count'], array(
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
