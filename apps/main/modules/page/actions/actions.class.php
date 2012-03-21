<?php

/**
 * page actions.
 *
 * @package    enter
 * @subpackage page
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pageActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->page = PageTable::getInstance()->getByToken($request['page']);
    $this->forwardUnless($this->page, 'redirect', 'index');

    $pageTitle = !empty($this->page->title) ? $this->page->title : $this->page->name;
    $this->getResponse()->setTitle($pageTitle.' – Enter.ru');

    $this->setVar('page', $this->page, true);
  }
}
