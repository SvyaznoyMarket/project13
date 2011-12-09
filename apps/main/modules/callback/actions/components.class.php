<?php

/**
 * user components.
 *
 * @package    enter
 * @subpackage user
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class callbackComponents extends myComponents
{
  public function executeNavigation()
  {

    $list = array(
      array(
        'name' => 'Обратная связь',
        'url'  => ''//url_for('default_show', array()),
      ),
    );

    $this->setVar('list', $list, false);
  }    

  public function executeFieldname(){
  }
  
  public function executeFieldemail(){
  }
  
  public function executeFieldtheme(){
  }
  
  public function executeFieldtext(){
  }

  function executeSeo_counters_advance() {
      
  }  
}
