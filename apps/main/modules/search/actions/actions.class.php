<?php

/**
 * search actions.
 *
 * @package    enter
 * @subpackage search
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property $searchString
 * @property $searchActions
 * @property $searchType
 * @property $productType
 * @property $forceSearch
 * @property $meanSearchString
 * @property $originalSearchString_quantity
 */
class searchActions extends myActions
{
  const PRODUCT_TYPE_ID = 1;
  private $_validateResult;

  public function executeIndex(sfWebRequest $request)
  {
    $this->getRequest()->setParameter('_template', 'product_catalog');

    $response = $this->loadQueryData($request);

    if (!$response || !is_array($response)) {
      return sfView::ERROR;
    }
    else if (isset($response['result']) && ('empty' == $response['result'])) {
      $this->setTemplate('empty');
      return sfView::SUCCESS;
    }

    if ($request->isXmlHttpRequest()) {
      $empty = true;

      if (is_array($response)) foreach ($response as $data)
      {
        if ($data['count'] > 0) {
          $empty = false;
          break;
        }
      }

      return $this->renderJson(array(
        'success' => !$empty,
        'data' => array(
          'content' => $empty ? $this->getPartial($this->getModuleName() . '/popup', array(
            'count' => 0,
            'searchString' => $this->searchString,
          )) : null
        ),
      ));
    }
    return sfView::SUCCESS;
  }

  private function loadQueryData(sfWebRequest $request)
  {
    $limit = sfConfig::get('app_product_max_items_on_category', 20);
    $page = $request->getParameter('page', 1);
    $offset = intval($page - 1) * $limit;
    $this->forward404If($offset < 0, 'Неверный номер страницы');

    $this->searchString = $request->getParameter('q');
    $this->forward404Unless($this->searchString);

    $title = 'Вы искали “' . $this->searchString . '”';

    if ($page) {
      $title .= ' – ' . $page;
    }
    $this->getResponse()->setTitle($title . ' – Enter.ru');

    $this->productType = !empty($request['product_type']) ? ProductTypeTable::getInstance()->find($request['product_type']) : false;

    $params = array(
      'request' => $this->searchString,
      'start' => $offset,
      'limit' => $limit,
      'type_id' => self::PRODUCT_TYPE_ID, // ищет только товары
      'product_type_id' => $this->productType ? array($this->productType->core_id) : array(),
      'is_product_type_first_only' => $this->productType ? 'false' : 'true',
      'use_mean' => true,
    );
    $response = Core::getInstance()->query('search.get', $params);

    if(!$response || !is_array($response))
      return null;

    $this->forceSearch = isset($response['forced_mean']) ? $response['forced_mean'] : false;
    $this->meanSearchString = isset($response['did_you_mean']) ? $response['did_you_mean'] : '';

    if (!$this->productType) {
      $this->productType = !empty($response[self::PRODUCT_TYPE_ID]['type_list'][0]['type_id']) ? ProductTypeTable::getInstance()->getByCoreId($response[self::PRODUCT_TYPE_ID]['type_list'][0]['type_id']) : false;
    }

    /** @var $productTypeList ProductType[] */
    $productTypeList = array();
    $data = $response[self::PRODUCT_TYPE_ID];

    if (!empty($data['type_list'])) {
      $coreIds = array();
      foreach ($data['type_list'] as $productTypeData)
      {
        $coreIds[$productTypeData['type_id']] = $productTypeData['count'];
      }

      $productTypeList = ProductTypeTable::getInstance()->getListByCoreIds(array_keys($coreIds), array('order' => '_index'));
      foreach ($productTypeList as $productType)
      {
        $productType->mapValue('_product_count', $coreIds[$productType->core_id]);

        if ($productType->id == $this->productType->id) {
          $this->productType->mapValue('_product_count', $productType->_product_count);
        }
      }
    }

    $this->setVar('searchString', $this->searchString);
    $this->setVar('productPager', $this->getProductPager($data), true);
    $this->setVar('productTypeList', $productTypeList, true);
    $this->setVar('resultCount', $response[self::PRODUCT_TYPE_ID]['count'], true);
    $this->setVar('view', $request->getParameter('view', 'compact'));
    return $response;
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

    $response = $this->loadQueryData($request);

    if (!$response) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Ошбика. Не удалось получить результаты поиска.';
      return $this->_refuse();
    } else if (isset($response['result']) && ('empty' == $response['result'])) {
      $this->setTemplate('emptyAjax');
    }

    $data = $response[self::PRODUCT_TYPE_ID];
    $this->setVar('searchString', $this->searchString, false);
    $this->setVar('productPager', $this->getProductPager($data), true);

    return sfView::SUCCESS;
  }

  protected function getProductPager(array $data)
  {
    $list = RepositoryManager::getProduct()->getListById($data['data'], true);
    $pager = new FilledPager(
      $list,
      isset($this->productType->_product_count) ? $this->productType->_product_count : 0,
      sfConfig::get('app_product_max_items_on_category', 20)
    );
    $pager->setPage($this->getRequest()->getParameter('page', 1));
    $pager->init();
    return $pager;
  }

  private function _refuse()
  {
    return $this->renderJson(array(
      'success' => $this->_validateResult['success'],
      'data' => array(
        'error' => $this->_validateResult['error'],
      ),
    ));
  }
}
