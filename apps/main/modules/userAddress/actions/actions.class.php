<?php

/**
 * userAddress actions.
 *
 * @package    enter
 * @subpackage userAddress
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userAddressActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->userAddressList = $this->getUser()->getGuardUser()->getAddressList();
  }

  public function executeSave(sfWebRequest $request)
  {
    $this->form = new UserAddressForm();

    $this->form->bind($request->getParameter($this->form->getName()));
    //$this->form->bind(array('address' => 'xcdfksd kjsdjfklsdjf sjdkfjs ', 'name' => 'ИМЯ!!!!', ));


    myDebug::dump($request->getParameter($this->form->getName()));
    myDebug::dump($this->form->getObject());
    if ($this->form->isValid())
    {
      try
      {
        //$this->form->bind(array('user_id' => $this->getUser()->getGuardUser()->id,));
        $this->form->getObject()->user_id = $this->getUser()->getGuardUser()->id;
        $this->form->save();
        die('was seved');
      }
      catch (Exception $e)
      {
        $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$e->getMessage());
        die('wasn\'t seved');
      }
    }
    else
    {
      $this->getLogger()->err('{'.__CLASS__.'} create: can\'t save form: '.$this->form->getErrorSchema());
    }

    $this->redirect('userAddress');

  }
}
