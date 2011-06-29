<?php
/**
 * newsCategory components.
 *
 * @package    newsCategory
 * @subpackage product
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsCategoryComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param myDoctrineCollection $newsCategoryList
  */
  public function executeList()
  {
    foreach ($this->newsCategoryList as $newsCategory)
    {
      $list[] = array(
        'name' => (string)$newsCategory,
        'url'  => url_for('newsCategory_show', $newsCategory),
      );
    }

    $this->setVar('list', $list, true);
  }
}