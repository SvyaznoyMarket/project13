<?php

/**
 * product components.
 *
 * @package    enter
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param Product $product Товар
  */
  public function executeShow()
  {

  }
 /**
  * Executes pager component
  *
  * @param myDoctrinePager $productPager Листалка товаров
  */
  public function executePager()
  {
    $this->setVar('productList', $this->productPager->getResults(), true);
  }
 /**
  * Executes sorting component
  *
  * @param array $productSorting Сортировка списка товаров
  */
  public function executeSorting()
  {
    $list = array();

    $active = $this->productSorting->getActive();
    foreach ($this->productSorting->getList() as $item)
    {
      if ($active['name'] == $item['name'])
      {
        $item['direction'] = 'asc' == $item['direction'] ? 'desc' : 'asc';
      }
      $list[] = array_merge($item, array(
        'url' => replace_url_for('sort', implode('-', array($item['name'], $item['direction'])))
      ));
    }

    $this->setVar('list', $list, true);
    $this->setVar('active', $active, true);
  }
 /**
  * Executes list component
  *
  * @param myDoctrineCollection $productList Список товаров
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->productList as $product)
    {
      $list[] = array(
        'name'     => (string)$product,
        'creator'  => (string)$product->Creator,
        'price'    => $product->formatted_price,
        'has_link' => $product['view_show'],
        'product'  => $product,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes pagination component
  *
  * @param myDoctrinePager $productPager Листалка товаров
  */
  public function executePagination()
  {
    if (!$this->productPager->haveToPaginate())
    {
      return sfView::NONE;
    }
  }
 /**
  * Executes property component
  *
  * @param Product $product Товар
  */
  public function executeProperty()
  {
    $list = array();
    foreach ($this->product['Parameter'] as $parameter)
    {
      $list[] = array(
        'name'  => $parameter->getName(),
        'value' => $parameter->getValue(),
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes property_grouped component
  *
  * @param Product $product Товар
  */
  public function executeProperty_grouped()
  {
    $list = array();
    foreach ($this->product['ParameterGroup'] as $parameterGroup)
    {
      $parameters = array();
      foreach ($parameterGroup->getParameter() as $parameter)
      {
        $parameters[] = array(
          'name'  => $parameter->getName(),
          'value' => $parameter->getValue(),
        );
      }

      $list[] = array(
        'name'       => $parameterGroup->getName(),
        'parameters' => $parameters,
      );
    }

    $this->setVar('list', $list, true);
  }
}
