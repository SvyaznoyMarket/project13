<?php

/**
 * page components.
 *
 * @package    enter
 * @subpackage page
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pageComponents extends myComponents
{
/**
  * Executes form component
  *
  * @param array $form Форма страницы
  */
  public function executeForm()
  {
    if (empty($this->form))
    {
      $this->form = new PageForm();
    }
  }
}
