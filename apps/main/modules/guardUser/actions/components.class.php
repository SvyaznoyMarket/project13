<?php

/**
 * guardUser components.
 *
 * @package    enter
 * @subpackage guardUser
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class guardUserComponents extends myComponents
{
/**
  * Executes form_signin component
  *
  * @param UserFormSignin $form Форма авторизации
  */
  public function executeForm_signin()
  {
    if (!($this->form instanceof UserFormSignin))
    {
      $this->form = new UserFormSignin();
    }
  }
}
