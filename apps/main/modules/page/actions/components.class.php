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
     // return sfView::NONE;
    }

    if (!$this->view)
    {
      $this->view = 'default';
    }

    $list = array(
      'about'  => array(
        'name'  => 'О нас',
        'links' => array(
          array('token' => 'about_company'),
          array('token'=>'callback','url' => 'callback', 'name' => 'Обратная связь'),
        ),
      ),
      'buying' => array(
        'name'  => 'Покупки в Enter',
        'links' => array(
          array('token' => 'how_make_order', 'add_to_name' => '?'),
          array('token' => 'how_get_order', 'add_to_name' => '?'),
          array('token' => 'how_pay', 'add_to_name' => '?'),
        ),
      ),
    );

    foreach ($list as &$item)
    {
      foreach ($item['links'] as &$link)
      {
        if (isset($link['token'])) $page = PageTable::getInstance()->findOneByToken($link['token']);
        if (!$page || !isset($page)){
        }
        else{
            $link['name'] = $page->name.(isset($link['add_to_name']) ? $link['add_to_name'] : '');
            $link['url'] = url_for('default_show', array('page' => $page->token));
        } 
      } if (isset($link)) unset($link);
    } if (isset($item)) unset($item);

    $this->setVar('list', $list, true);
  }

  public function executeNavigation()
  {
    if (!isset($this->page) || !$this->page instanceof Page)
    {
      return sfView::NONE;
    }

    $list = array(
/*      array(
        'name' => 'Enter',
        'url'  => url_for('homepage'),
      ),*/
      array(
        'name' => $this->page->name,
        'url'  => url_for('default_show', array('page' => $this->page->token)),
      ),
    );

    $this->setVar('list', $list, false);
  }
  
  public function executeLink_rel_canonical(){
    $request = sfContext::getInstance()->getRequest(); 
    $page = $request['page'];
    #var_dump( $page );
    #echo intVal($page) .'=='. $page;
    if ($page && strval(intVal($page)) == $page){
        $this->setVar('show_link', false);              
    } else {
        $this->setVar('show_link', true);   
        $info = $request->getPathInfoArray();
        #print_r($info);
        $ar = explode('?',$info['REQUEST_URI']);
        $path = str_replace(array('_filter', '_tag'), '', $ar[0]);
        if ($path == "/") {
            $path = '';
        } 
        
        $this->setVar('href', 'http://' . $info['SERVER_NAME'] . $path);           
    }
  }

}
