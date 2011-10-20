<?php

/**
 * tag components.
 *
 * @package    enter
 * @subpackage tag
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tagComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param myDoctrineCollection $tagList Список тегов
  */
  public function executeList()
  {
    $list = array();

    foreach ($this->tagList as $tag)
    {
      $list[] = array(
        'name'  => (string)$tag,
        'token' => $tag->token,
        'url'   => url_for('tag_show', array('tag' => $tag->token)),
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes navigation component
  *
  * @param Tag $tag Тег
  */
  public function executeNavigation()
  {
    $list = array();

    $list[] = array(
      'name' => 'Теги',
      'url'  => url_for('tag'),
    );

    if ($this->tag)
    {
      $list[] = array(
        'name' => (string)$this->tag,
        'url'  => url_for('tag_show', array('tag' => $this->tag->token)),
      );
    }

    $this->setVar('list', $list, false);
  }
}
