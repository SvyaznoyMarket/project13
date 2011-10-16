<?php

/**
 * page components.
 *
 * @package    enter
 * @subpackage page
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pageComponents extends myComponents
{
 /**
  * Executes form component
  *
  * @param array $form Форма страницы
  */
  public function executeForm()
  {
    if (empty($this->form))
    {
      $this->form = new PageForm();
    }
  }
 /**
  * Executes menu component
  *
  * @param Page $Page Страница
  */
  public function executeMenu()
  {
    if (!$this->page instanceof Page)
    {
      return sfView::NONE;
    }

    $this->view = 'about';

    $views = array(
      'about' => array('about', 'history', 'benefits', 'clients', 'details', 'contacts'),
    );
    
    foreach ($views as $view => $tokens)
    {
      if (in_array($this->page->token, $tokens))
      {
        $this->view = $view;
        break;
      }
    }
    
    $list = array();
    foreach ($views[$this->view] as $token)
    {
      $page = PageTable::getInstance()->findOneByToken($token);
      if (!$page) continue;
      
      $list[] = array(
        'token' => $page->token,
        'name'  => $page->name,
        'url'   => url_for('default_show', array('page' => $page->token)),
      );
    }
    
    $this->setVar('list', $list, true);
  }
}
