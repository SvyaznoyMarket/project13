<?php

/**
 * line actions.
 *
 * @package    enter
 * @subpackage line
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 */
class lineActions extends myActions
{
  public function preExecute()
  {
    parent::postExecute();

    $this->getRequest()->setParameter('_template', 'product_card');
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  public function executeCard(sfWebRequest $request)
  {
    /** @var $line ProductLine */
    $line = $this->getRoute()->getObject();
    $productLine = RepositoryManager::getProductLine()->getByIdWithProducts($line->core_id);
    // init views
    $this->setVar('line', $line);
    $this->setVar('productLine', $productLine);
    $this->setVar('view', $request->getParameter('view', 'compact'));
  }
}
