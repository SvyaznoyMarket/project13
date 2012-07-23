<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 12:40
 * To change this template use File | Settings | File Templates.
 */
require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(ROOT_PATH.'lib/MainMenuBuilder.php');
require_once(VIEW_PATH.'dataObject/CategoryShortData.php');
require_once(VIEW_PATH.'dataObject/pageObject/mainMenuData.php');

class catalogController
{
  /**
   * @param Response $response
   * @param array $params
   */
  public function MainMenu(Response $response, $params=array()){
    TimeDebug::start('App::getCategory()->getTreeAsArray');
    $catalog = App::getCategory()->getTreeAsArray(Null, 3);
    TimeDebug::end('App::getCategory()->getTreeAsArray');
    $menu = array();
    TimeDebug::start('MainMenu::getContainers');
    foreach($catalog as $rootCategory){
      $category = new CategoryShortData();
      $category->setId($rootCategory['id']);
      $category->setLink($rootCategory['link']);
      $category->setName($rootCategory['name']);
      $category->setToken($rootCategory['token']);

      $menu[] = array(
        'category' => $category,
        'blocks'   => MainMenuBuilder::getContainers($rootCategory['children'], 4)
      );
    }
    TimeDebug::end('MainMenu::getContainers');
    TimeDebug::start('MainMenu::render');
    $response->setContent(App::getRenderer()->renderFile('mainMenu', array('Menu' => new mainMenuData($menu))));
    TimeDebug::end('MainMenu::render');
  }
}