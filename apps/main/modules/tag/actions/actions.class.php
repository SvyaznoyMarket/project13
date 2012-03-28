<?php

/**
 * tag actions.
 *
 * @package    enter
 * @subpackage tag
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tagActions extends myActions
{
  private $_validateResult;

  public function preExecute()
  {
    parent::preExecute();

    $this->getRequest()->setParameter('_template', 'product_catalog');
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->tagList = TagTable::getInstance()->getList(array(
      'select'       => 'tag.id, tag.name, tag.token',
      'order'        => 'tag.name',
      'limit'        => 200,
      //'has_products' => true,
    ));
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->tag = !empty($request['tag']) ? TagTable::getInstance()->getByToken($request['tag']) : false;
    $this->forward404Unless($this->tag);

    $this->productTypeList = ProductTypeTable::getInstance()->getListByTag($this->tag, array(
      'select'            => 'productType.id, productType.name',
      'group'             => 'productType.id, productType.name',
      'with_productCount' => true,
    ));

    $this->productType = !empty($request['productType']) ? ProductTypeTable::getInstance()->findOneByToken($request['productType']) : false;
    if (!$this->productType)
    {
      $this->productType = isset($this->productTypeList[0]) ? $this->productTypeList[0] : false;
    }

    $table = ProductTable::getInstance();

    $q = $table->createBaseQuery(array(
      'view' => 'list',
    ));
    $table->setQueryForFilter($q, array(
      'tag'  => $this->tag,
      'type' => $this->productType ? array($this->productType->id) : array(),
    ));

    $this->productPager = $this->getPager('Product', $q, sfConfig::get('app_product_max_items_on_category', 20), array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));

    $this->setVar('noSorting', true);

    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }


 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShowAjax(sfWebRequest $request)
  {
    if (!isset($request['tag'])) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Не получен tag.';
      return $this->_refuse();
    }
    $this->tag = !empty($request['tag']) ? TagTable::getInstance()->getByToken($request['tag']) : false;
    if (!$this->tag) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Tag не найден.';
      return $this->_refuse();
    }

    $this->productTypeList = ProductTypeTable::getInstance()->getListByTag($this->tag, array(
      'select'            => 'productType.id, productType.name',
      'group'             => 'productType.id, productType.name',
      'with_productCount' => true,
    ));

    $this->productType = !empty($request['productType']) ? ProductTypeTable::getInstance()->findOneByToken($request['productType']) : false;
    if (!$this->productType)
    {
      $this->productType = isset($this->productTypeList[0]) ? $this->productTypeList[0] : false;
    }

    $table = ProductTable::getInstance();

    $q = $table->createBaseQuery(array(
      'view' => 'list',
    ));
    $table->setQueryForFilter($q, array(
      'tag'  => $this->tag,
      'type' => $this->productType ? array($this->productType->id) : array(),
    ));

    if (isset($request['num'])) $limit = $request['num'];
    else $limit = sfConfig::get('app_product_max_items_on_category', 20);
    $this->productPager = $this->getPager('Product', $q, $limit, array(
      'with_properties' => 'expanded' == $request['view'] ? true : false,
      'property_view'   => 'expanded' == $request['view'] ? 'list' : false,
    ));

    $this->setVar('noSorting', true);

    if ($request['page'] > $this->productPager->getLastPage() ) {
      $this->_validateResult['success'] = false;
      $this->_validateResult['error'] = 'Номер страницы превышает максимальный для списка';
      return $this->_refuse();
    }
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
