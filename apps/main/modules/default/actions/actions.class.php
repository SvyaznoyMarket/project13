<?php

/**
 * default actions.
 *
 * @package    enter
 * @subpackage default
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
 /**
  * Executes error404 action
  *
  * @param sfRequest $request A request object
  */
  public function executeError404(sfWebRequest $request)
  {
  }
 /**
  * Executes qrcode action
  *
  * @param sfRequest $request A request object
  */
  public function executeQrcode(sfWebRequest $request)
  {
    $type = 'product';
    $ids = array(1, 2, 3, 4, 5, 6, 7);

    switch ($type)
    {
      case 'product';
        $tokens = array('product-1-1', 'product-1-2', 'product-1-3', 'product-1-4', 'product-1-5', 'product-1-6', 'product-1-7'); // преобразовать core_id в token
        $request->setParameter('products', $tokens);
        $this->forward('product', 'list');
        break;
    }
  }
}
