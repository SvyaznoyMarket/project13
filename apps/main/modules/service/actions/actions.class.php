<?php

/**
 * service actions.
 *
 * @core
 * @package    enter
 * @subpackage service
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceActions extends myActions
{
  public function preExecute()
  {
    parent::preExecute();
    $this->getRequest()->setParameter('_template', 'service');
  }

  public function executeIndex(sfWebRequest $request)
  {
    $categoryTree = RepositoryManager::getService()->getCategoryRootTree(2);
    $this->getResponse()->setTitle('F1 - ' . $categoryTree->getName() . ' – Enter.ru');
    $this->setVar('categoryTree', $categoryTree, true);
  }

  public function executeShow(sfWebRequest $request)
  {
    $service = RepositoryManager::getService()->getByToken($request['service']);
    $this->forward404If($service === null);
    $this->getResponse()->setTitle('F1 - ' . $service->getName(). ' – Enter.ru');
    $idList = $service->getAlikeIdList();
    $idList = array_splice($idList,0,4);
    $list = RepositoryManager::getService()->getListById($idList);
    $service->setAlikeList($list);
    $this->setVar('service', $service);
  }

  public function executeCategory(sfWebRequest $request)
  {
    $categoryTree = RepositoryManager::getService()->getCategoryRootTree(3);
    $category = RepositoryManager::getService()->getCategoryTreeByToken($request->getParameter('category'));
    $this->forward404If($category === null);
    $this->forward404If($category->getLevel()<2);
    if($category->getLevel()==2){
      $category = $category->getFirstChild();
    }
    $this->forward404If($category === null);

    $categoryList = $category->getChildren();
    RepositoryManager::getService()->loadServiceList($categoryList);

    $this->getResponse()->setTitle('F1 - ' . $category->getName() . ' – Enter.ru');
    $this->setVar('categoryTree', $categoryTree);
    $this->setVar('category', $category);
    $this->setVar('categoryList', $categoryList);
  }
}
