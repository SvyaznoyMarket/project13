<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 23.08.12
 * Time: 15:08
 * To change this template use File | Settings | File Templates.
 */
class Tree
{
  private $currPos = 0;

  /** @var TreeElem[] */
  private $elems = array();

  public function __construct(TreeElem $treeElem){
    $this->elems[] = $treeElem;
    $this->currPos = 0;
  }

  public function root(){
    $elem = $this->elems[$this->currPos];
    while(true){
      if($elem->getParent()){
        $this->currPos = 0;
        $this->elems = array($elem->getParent());
      }
      else{
        break;
      }
    }
  }

  public function next(){
    if($this->currPos == count($this->elems)){
      return false;
    }
    $this->currLvl++;
    return true;
  }

  public function prev(){
    if($this->currPos == 0){
      return false;
    }
    $this->currLvl--;
    return true;
  }

  public function down(){
    $elem = $this->elems[$this->currPos];
    if(!$elem->hasChildren()){
      return false;
    }

    $this->elems = $elem->getChildren();
    $this->currPos = 0;
  }

  public function up(){
    $elem = $this->elems[$this->currPos];
    if(!$elem->getParent()){
      return false;
    }
    $this->elems = array($elem->getParent());
    $this->currPos = 0;
  }

  public function getElem(){
    $elem = $this->elems[$this->currPos];
    return $elem->getData();
  }

  public function hasChildren(){
    $elem = $this->elems[$this->currPos];
    return $elem->hasChildren();
  }
}

class TreeElem{

  private $parent = null;
  private $children = array();
  private $data;

  public function __construct($parent, $data){
    $this->parent = $parent;
    $this->data = $data;
  }

  public function hasChildren(){
    return count($this->data > 0);
  }

  public function getChildren(){
    return $this->children;
  }

  public function getParent(){
    return $this->parent;
  }

  public function getData(){
    return $this->data;
  }

  public function addChild(TreeElem $treeElem){
    $this->children[] = $treeElem;
  }
}
