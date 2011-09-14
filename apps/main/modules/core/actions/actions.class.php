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

    /*$order = new Order();
    $order->fromArray(array(
      'recipient_first_name' => 'Я',
      'recipient_last_name' => 'ТЫ',
      'recipient_middle_name' => 'ВЫ',
    ));*/

    $user = UserTable::getInstance()->findOneById(2);
    //if (!$response = $core->createOrder($order))
    if (!$response = $core->createUser($user))
    {
        myDebug::dump($core->getError());
    }
    else
    {
      myDebug::dump($response);
    };

    $tags = $user->getTag();
    foreach ($tags as $tag)
    {
      if (!$response = $core->createUserTag($tag))
      {
          myDebug::dump($core->getError());
      }
      else
      {
        myDebug::dump($response);
      };
    }

    $addresses = $user->getAddress();
    foreach ($addresses as $address)
    {
      if (!$response = $core->createUserAddress($address))
      {
          myDebug::dump($core->getError());
      }
      else
      {
        myDebug::dump($response);
      };
    }

    $order = $user->getOrder()->getFirst();
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
