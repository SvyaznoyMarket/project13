<?php

/**
 * shop actions.
 *
 * @package    enter
 * @subpackage shop
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class shopActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->region = !empty($request['region']) ? RegionTable::getInstance()->getByToken($request['region']) : RegionTable::getInstance()->getDefault();
    $this->forward404Unless($this->region, 'Region not found');
  }
 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->shop = $this->getRoute()->getObject();
  }
}
