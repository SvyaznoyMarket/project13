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
  
  function executeHeader_meta_og() {
      $defaultDescription = 'Enter - новый способ покупать. Любой из 20000 товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.';
      $description = '';
      if ($this->product->description) {
          $description = $this->product->description;
      } elseif ($this->product->tagline) { 
          $description = $this->product->tagline;
      } else {
          $description = $defaultDescription;
      }
      $this->setVar('title', $this->product->name);
      $this->setVar('description', $description);
      $this->setVar('photo', $this->product->getMainPhotoUrl(3));
  }
}
