<?php
namespace light;

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(ROOT_PATH.'lib/MainMenuBuilder.php');

class serviceController
{
  /**
   * @param Response $response
   * @param array $params
   */
  public function index(Response $response, $params=array()) {
    TimeDebug::start('Service.index::render');

    $categoryTree = App::getService()->getCategoryRootTree(2);
    $renderer = App::getHtmlRenderer();
    $breadCrumbList = $categoryTree->getNavigation();
    $renderer->addParameter('breadCrumbList', $breadCrumbList);
    $renderer->addParameter('categoryTree', $categoryTree);
    $renderer->addParameter('pageTitle', 'Услуги F1');
    $renderer->setTitle('F1 - ' . $categoryTree->getName() . ' – Enter.ru');
    $renderer->setPage('service/index');
    $response->setContent($renderer->render());
    TimeDebug::end('Service.index::render');
  }

  public function category(Response $response, $params = array()) {
      $categoryTree = App::getService()->getCategoryRootTree(3);
      $category = App::getService()->getCategoryTreeByToken($_REQUEST['category']);

      #$this->forward404If($category->getLevel()<2);

      if($category->getLevel()==2){
          $category = $category->getFirstChild();
      }
      #$this->forward404If($category === null);

      $categoryList = $category->getChildren();
      App::getService()->loadServiceList($categoryList);

      $renderer = App::getHtmlRenderer();
      $renderer->setTitle('F1 - ' . $category->getName() . ' – Enter.ru');
      $renderer->addParameter('categoryTree', $categoryTree);
      $renderer->addParameter('category', $category);
      $renderer->addParameter('categoryList', $categoryList);
      $renderer->addParameter('breadCrumbList', $category->getNavigation());
      $renderer->addParameter('pageTitle', $category->getName());
      $renderer->setPage('service/category');
      $response->setContent($renderer->render());
  }

  public function show(Response $response, $params = array()) {
      $service = App::getService()->getByToken($_REQUEST['service']);
      $renderer = App::getHtmlRenderer();
      $renderer->setTitle('F1 - ' . $service->getName(). ' – Enter.ru');
      $idList = (array)$service->getAlikeIdList();
      $idList = array_splice($idList,0,4);
      $list = App::getService()->getListById($idList);
      $service->setAlikeList($list);
      $renderer->addParameter('service', $service);
      $renderer->addParameter('pageTitle', $service->getName());
      $renderer->addParameter('breadCrumbList', $service->getNavigation());

      #include_component('default', 'navigation', array('list' => $service->getNavigation()));
      $renderer->setPage('service/show');
      $response->setContent($renderer->render());
  }
}