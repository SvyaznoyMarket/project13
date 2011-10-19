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
    $offset = abs(intval($request->getParameter('page')) - 1);
    $limit = sfConfig::get('app_product_max_items_on_category', 20);

    //$this->searchString = iconv('windows-1251', 'utf-8', $request['q']);
    $this->searchString = $request['q'];
    if (empty($this->searchString))
    {
      return sfView::NONE;
    }

    // запрос к core
    $response = Core::getInstance()->query('search.get', array(
      'request' => $this->searchString,
      'start'   => $offset,
      'limit'   => $limit,
    ));
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

    $categories = array();
    $pagers = array();
    if (is_array($response)) foreach ($response as $core_id => $data)
    {
      $type = $this->getSearchTypeByCoreId($core_id);
      if (null == $type) continue;

      $categories[$type] = array();

      $pagers[$type] = call_user_func_array(array($this, 'get'.ucfirst($type).'Pager'), array($data));

      if (('product' == $type) && !empty($data['types']))
      {
        $coreIds = array_keys($data['types']);
        foreach (ProductTypeTable::getInstance()->getListByCoreIds($coreIds) as $productType)
        {
          $categories[$type][] = array(
            'record' => $productType,
            'count'  => $data['types'][$productType->core_id],
          );
        }
      }
    }

    $this->categories = $categories;
    $this->pagers = $pagers;
  }


  protected function getSearchTypeByCoreId($core_id)
  {
    $types = array(
      1 => 'product',
      2 => 'news',
    );

    return isset($types[$core_id]) ? $types[$core_id] : null;
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
