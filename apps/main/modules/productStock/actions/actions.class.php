<?php

/**
 * productStock actions.
 *
 * @package    enter
 * @subpackage productStock
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @method sfWebResponse getResponse
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
    $token = preg_split('#/#', $request->getParameter("product"), -1, PREG_SPLIT_NO_EMPTY);
    $token = end($token);
    $product = RepositoryManager::getProduct()->getByToken($token, true);
    $this->forward404If($product == null);

    $this->getResponse()->setTitle(sprintf(
      'Где купить %s в магазинах Enter - интернет-магазин Enter.ru',
      $product->getName(),
      $product->getName()
    ));
    $this->getResponse()->addMeta('description', sprintf(
      '',
      $product->getName(),
      $product->getName()
    ));
    $this->getResponse()->addMeta('keywords', sprintf(
      '%s где купить %s',
      mb_strtolower($product->getName()),
      mb_strtolower($this->getUser()->getRegion('region'))
    ));

    $this->setVar('product', $product);
  }
}
