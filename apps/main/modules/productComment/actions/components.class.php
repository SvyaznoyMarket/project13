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
        'id'             => $productComment->id,
        'level'          => $productComment->level,
        'date'           => $productComment->created_at,
        'author'         => (string)$productComment->User,
        'content'        => $productComment->content,
        'answer_url'     => url_for(array('sf_route' => 'productComment_new', 'sf_subject' => $this->product, 'parent' => $productComment->id)),
        'productComment' => $productComment,
        'helpful'        => $productComment->helpful,
        'unhelpful'      => $productComment->unhelpful,
      );
    }

    $this->setVar('list', $list, true);
  }
 /**
  * Executes form component
  *
  * @param Product $product Товар
  * @param ProductCommentForm $form Форма комментария товара
  */
  public function executeForm()
  {
    if (empty($this->form))
    {
      $this->form = new ProductCommentForm();
    }
  }
}
