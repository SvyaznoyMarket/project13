<?php

/**
 * cart actions.
 *
 * @package enter
 * @subpackage cart
 * @author Связной Маркет
 * @version SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cart_Actions extends myActions
{

  private $_validateResult;

  /**
   * Executes index action
   *
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->setVar('cart', $this->getUser()->getCart(), true);

    $this->setVar('selectCredit', (bool)(!empty($_COOKIE['credit_on']) && ($_COOKIE['credit_on'] == 1)));

    $this->getUser()->setCacheCookie();
  }
}