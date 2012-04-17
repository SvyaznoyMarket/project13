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
    $this->getResponse()->setTitle($pageTitle . ' – Enter.ru');

    if ($this->page['level']) {
      $q = PageTable::getInstance()->createBaseQuery()
        ->addWhere('page.root_id = ? AND page.level = ?', array($this->page['root_id'], 0))
        ->limit(1);
      $rootPage = $q->fetchOne();
      $addToBreadcrumbs[] = array('name' => $rootPage['name'], 'url' => $this->generateUrl('default_show', array('page' => $rootPage['token'],)));
    }
    $addToBreadcrumbs[] = array('name' => $this->page['name']);

    $this->setVar('page', $this->page, true);
    $this->setVar('addToBreadcrumbs', $addToBreadcrumbs, true);
  }
}
