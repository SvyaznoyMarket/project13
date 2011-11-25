<?php

/**
 * productCard components.
 *
 * @package    enter
 * @subpackage productCard
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCardComponents extends myComponents
{
/**
  * Executes navigation component
  *
  * @param Product $product Товар
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => 'Главная',
      'url'  => url_for('@homepage'),
    );
    $list[] = array(
      'name' => 'Каталог товаров',
      'url'  => url_for('@productCatalog'),
    );
    if (isset($this->product->Category))
    {
      foreach ($this->product->Category as $c) {
        $list[] = array(
          'name' => $c->name,
          'url'  => url_for('productCatalog_category', $c),
        );
        break;
      }
    }
    if (isset($this->product->Creator))
    {
      $list[] = array(
        'name' => $this->product->Creator->name,
        'url'  => url_for(array('sf_route' => 'productCatalog_creator', 'sf_subject' => $this->product->Category, 'creator' => $this->product->Creator)),
      );
    }
    $list[] = array(
      'name' => $this->product->name,
      'url'  => url_for(array('sf_route' => 'productCard', 'sf_subject' => $this->product)),
    );

    $this->setVar('list', $list, true);
  }
}
