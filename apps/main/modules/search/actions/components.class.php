<?php

/**
 * search components.
 *
 * @package    enter
 * @subpackage search
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class searchComponents extends myComponents
{
 /**
  * Executes form component
  *
  * @param string $searchString Поисковая фраза
  */
  public function executeForm()
  {
    if (!in_array($this->view, array('default', 'main')))

    {
      $this->view = 'default';
    }

    if (empty($this->searchString))
    {
      $this->searchString = '';
    }
  }
 /**
  * Executes navigation component
  *
  * @param string $searchString Поисковая фраза
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => "Поиск (".htmlentities($this->searchString).")",
      'url'  => url_for('search', array('searchString' => $this->searchString)),
    );

    $this->setVar('list', $list, false);
  }
 /**
  * Executes categories component
  *
  * @param array $list Категории поиска
  */
  public function executeCategories()
  {
  }
}
