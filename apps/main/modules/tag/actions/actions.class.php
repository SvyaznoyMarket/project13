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

    $table = ProductTable::getInstance();

    $q = $table->createBaseQuery();
    $table->setQueryForFilter($q, array(
      'tag'  => $this->tag,
      'type' => !empty($request['product_types']) ? $request['product_types'] : false,
    ));

    $this->productPager = $this->getPager('Product', $q, array(
      'limit' => sfConfig::get('app_product_max_items_on_category', 20),
    ));
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');

    $this->productTypeList = $this->getProductTypes($request);
  }



  protected function getProductTypes($request)
  {
    $productTypeList = ProductTypeTable::getInstance()->getListByTag($this->tag, array(
      'select'            => 'productType.id, productType.name',
      'group'             => 'productType.id, productType.name',
      'with_productCount' => true,
    ));

    $ids = is_array($request['product_types']) ? $request['product_types'] : array();
    foreach ($productTypeList as $productType)
    {
      $productType->mapValue('_selected', in_array($productType->id, $ids));
    }

    return $productTypeList;
  }
}
