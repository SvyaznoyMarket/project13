<?php

/**
 * productCard_ actions.
 *
 * @package    enter
 * @subpackage productCard_
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property ProductEntity $product
 */
class productCard_Actions extends myActions
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
    $product_token = end(explode('/', $request['product']));

    $this->product = RepositoryManager::getProduct()->getByToken($product_token);
  }
}
