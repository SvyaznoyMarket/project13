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
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');
  }
}
