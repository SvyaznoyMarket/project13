<?php

/**
 * productComment components.
 *
 * @package    enter
 * @subpackage productComment
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productCommentComponents extends myComponents
{
 /**
  * Executes list component
  *
  * @param Product $product Товар
  */
  public function executeList()
  {
    $list = array();
    foreach ($this->product->getCommentList() as $productComment)
    {
      $list[] = array(
        'date'    => $productComment->created_at,
        'author'  => (string)$productComment->User,
        'content' => $productComment->content,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes form component
  *
  * @param Product $product Товар
  */
  public function executeForm()
  {

  }
}
