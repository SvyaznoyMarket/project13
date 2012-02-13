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
  public function preExecute()
  {
    parent::postExecute();

    $this->getRequest()->setParameter('_template', 'product_stock');
  }
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
    $descr = '';
    $this->getResponse()->addMeta('description', sprintf(
        $descr,
        $this->product['name'],
        $this->product['name']
    ));
    $this->getResponse()->addMeta('keywords', sprintf('%s где купить %s', mb_strtolower($this->product['name']), mb_strtolower($this->getUser()->getRegion('region'))));
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
