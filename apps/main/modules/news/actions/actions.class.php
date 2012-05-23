<?php

/**
 * news actions.
 *
 * @package    enter
 * @subpackage news
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsActions extends myActions
{
  public function executeShow(sfWebRequest $request)
  {
    $this->news = $this->getRoute()->getObject();
  }

    public function executeIpad(sfWebRequest $request)
    {
        $this->getResponse()->setTitle('The new Ipad');
    }
}
