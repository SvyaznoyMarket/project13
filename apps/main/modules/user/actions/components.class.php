<?php

/**
 * user components.
 *
 * @package    enter
 * @subpackage user
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userComponents extends myComponents
{
/**
  * Executes profile component
  *
  */
  public function executeProfile()
  {
    if ($userType = !$this->getUser()->getType())
    {
      return sfView::NONE;
    }

    $this->view = $userType;
  }
/**
  * Executes profile_client component
  *
  */
  public function executeProfile_client()
  {
  }
/**
  * Executes profile_partner component
  *
  */
  public function executeProfile_partner()
  {
  }
}
