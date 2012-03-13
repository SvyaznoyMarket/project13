<?php

/**
 * productCard actions.
 *
 * @package    enter
 * @subpackage productCard
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCardSoaActions extends myActions
{
  public function preExecute()
  {
    parent::postExecute();

    $this->getRequest()->setParameter('_template', 'product_card');
  }

 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {

    //$this->product = ($request['product'] instanceof Product) ? $request['product'] : $this->getRoute()->getObject();

      $productAr = explode('/', $request['product']);
      //echo end($productAr);
      //die();

      try {
        $factory = new ProductFactory();
        $this->product = $factory->createProductFromCore(array('slug' => end($productAr)), true);
      } catch (ErrorException $e) {
         $this->forward404If($e->getMessage());
      }
//      myDebug::dump($this->product);
//      print_r($this->product);
//      die();
    $title = '%s - купить по цене %s руб. в Москве, %s - характеристиками и описанием и фото от интернет-магазина Enter.ru';
    $this->getResponse()->setTitle(sprintf(
        $title,
        $this->product->name,
        100,
        $this->product->name
    ));
    $descr = 'Интернет магазин Enter.ru предлагает купить: %s по цене %s руб. На нашем сайте Вы найдете подробное описание и характеристики товара %s с фото. Заказать понравившийся товар с доставкой по Москве можно у нас на сайте или по телефону 8 (800) 700-00-09.';
    $this->getResponse()->addMeta('description', sprintf(
        $descr,
        $this->product->name,
        100,
        $this->product->name
    ));
    $this->getResponse()->addMeta('keywords', sprintf('%s Москва интернет магазин купить куплю заказать продажа цены', $this->product->name));
    // :: SEO
    //
    // история просмотра товаров
    //$this->getUser()->getProductHistory()->addProduct($this->product);

    //$q = ProductTable::getInstance()->getQueryByKit($this->product);

//    $this->productPager = $this->getPager('Product', $q, 100,array(
//      'with_model'  => true,
//    ));
    //$this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');

    $this->view = 'compact';

  }
 /**
  * Executes preview action
  *
  * @param sfRequest $request A request object
  */
  public function executePreview(sfWebRequest $request)
  {
    $this->product = $this->getRoute()->getObject();
  }
}
