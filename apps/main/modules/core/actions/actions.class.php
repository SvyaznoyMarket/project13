<?php

/**
 * core actions.
 *
 * @package    enter
 * @subpackage core
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class coreActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $core = Core::getInstance();

    $order = new Order();
    $order->fromArray(array(
      'recipient_first_name' => 'Я',
      'recipient_last_name' => 'ТЫ',
      'recipient_middle_name' => 'ВЫ',
    ));

    if (!$response = $core->createOrder($order))
    {
        myDebug::dump($core->getError());
    }
    else
    {
      myDebug::dump($response);
    };

  }
}
