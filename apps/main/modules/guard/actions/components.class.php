<?php

/**
 * guard components.
 *
 * @package    enter
 * @subpackage guard
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class guardComponents extends myComponents
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
