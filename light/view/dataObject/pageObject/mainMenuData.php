<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 16.05.12
 * Time: 16:01
 * To change this template use File | Settings | File Templates.
 */
class mainMenuData implements Iterator {
  private $position = 0;
  private $elements = array(
  );

  public function __construct($menu = array()) {
    $this->position = 0;

    foreach($menu as $menuElem){
      if(isset($menuElem['category']) && isset($menuElem['blocks'])){
        $this->elements[] = array('category' => $menuElem['category'], 'blocks' => $menuElem['blocks']);
      }
    }
  }

  function rewind() {
    $this->position = 0;
  }

  function current() {
    return $this->elements[$this->position]['category'];
  }

  function key() {
    return $this->position;
  }

  function next() {
    ++$this->position;
  }

  function valid() {
    return (isset($this->elements[$this->position]) && isset($this->elements[$this->position]['category']));
  }

  /**
   * @param int $i
   * @return array
   */
  public function getBlock($i){
    if(isset($this->elements[$this->position]['blocks'][$i])){
      return $this->elements[$this->position]['blocks'][$i];
    }
    else{
      return array();
    }
  }
}
