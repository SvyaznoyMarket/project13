<?php

/**
 * page actions.
 *
 * @package    enter
 * @subpackage page
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pageActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->page = PageTable::getInstance()->getByToken($request['page']);
    $this->forward404Unless($this->page);
  }
 /**
  * Executes edit action
  *
  * @param sfRequest $request A request object
  */
  public function executeEdit(sfWebRequest $request)
  {
    $this->page = $this->getRoute()->getObject();
  }
}
