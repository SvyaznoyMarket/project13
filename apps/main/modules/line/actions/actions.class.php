<?php

/**
 * line actions.
 *
 * @package    enter
 * @subpackage line
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class lineActions extends myActions
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
    $this->forward('default', 'module');
  }

 /**
  * Executes Card action
  *
  * @param sfRequest $request A request object
  */
  public function executeCard(sfWebRequest $request)
  {
    $this->line = $this->getRoute()->getObject();

    $main_product = ProductTable::getInstance()->getByLine($this->line);
    $this->forward404If(!$main_product);

    $region = $this->getUser()->getRegion('name');
    $title = "{$main_product->name} - купить по цене {$main_product->getFormattedPrice()} руб. с доставкой и сборкой в г. {$region}. {$main_product->name} - описание, фотографии, отзывы в магазине Enter";
    $this->getResponse()->setTitle($title);

    $q = ProductTable::getInstance()->getQueryByLine($this->line, array('with_main' => false, 'view' => 'list', ));

    $this->productPager = $this->getPager('Product', $q, $q->count());
    $this->forward404If($request['page'] > $this->productPager->getLastPage(), 'Номер страницы превышает максимальный для списка');

    $this->view = 'compact';
    $this->setVar('product_id', $main_product->id);
  }
}
