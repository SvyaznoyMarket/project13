<?php

/**
 * userProductNotice components.
 *
 * @package    enter
 * @subpackage userProductNotice
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductNoticeComponents extends myComponents
{
 /**
  * Executes form component
  *
  */
  public function executeForm()
  {
    if (!$this->form)
    {
      $this->form = new UserProductNoticeForm();
    }
  }
}
