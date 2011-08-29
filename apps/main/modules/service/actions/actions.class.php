<?php

/**
 * service actions.
 *
 * @package    enter
 * @subpackage service
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('list');
  }

 /**
  * Executes show action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->service = $this->getRoute()->getObject();
  }

 /**
  * Executes category action
  *
  * @param sfRequest $request A request object
  */
  public function executeCategory(sfWebRequest $request)
  {
    $list = ServiceCategoryTable::getInstance()->getList();

    $this->setVar('list', $list, true);
  }

 /**
  * Executes list action
  *
  * @param sfRequest $request A request object
  */
  public function executeList(sfWebRequest $request)
  {
    $serviceCategory = $this->getRoute()->getObject();
    $list = $serviceCategory->getServiceList();

    $this->setVar('list', $list, true);
  }
}
