<?php

/**
 * productStock actions.
 *
 * @package    enter
 * @subpackage productStock
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productStockActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
    
    // SEO ::
    $this->product->description = '<noindex>' . $this->product->description . '</noindex>';
    $title = 'Где купить %s в магазинах Enter - интернет-магазин Enter.ru';
    $this->getResponse()->setTitle(sprintf(
        $title, 
        $this->product['name'], 
        $this->product['name']
    ));
    $descr = 'Интернет магазин Enter.ru предлагает ознакомиться с отзывами владельцев товара %s. На этой странице Вы можете прочитать отзывы покупателей о товаре %s, а так же оставить свое мнение.';
    $this->getResponse()->addMeta('description', sprintf(
        $descr,
        $this->product['name'], 
        $this->product['name']
    ));
    $this->getResponse()->addMeta('keywords', sprintf('%s отзывы мнения покупателей владельцев пользователей', $this->product['name']));
    // :: SEO

//    $title = '«Где купить»: ' . $this->product['name'] . ' в магазинах "Enter"';
//    $mainCategory = $this->product->getMainCategory();
//    $title .= ' – '.$mainCategory;
//    if ($mainCategory)
//    {
//      $rootCategory = $mainCategory->getRootCategory();
//      if ($rootCategory->id !== $mainCategory->id)
//      {
//        $title .= ' – '.$rootCategory;
//      }
//    }
//    $this->getResponse()->setTitle($title.' – Enter.ru');
  }
}
