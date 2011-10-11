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
	$this->sortParams = array(
		'created_asc'  => 'Дате (возрастание)',
		'created_desc' => 'Дате (убывание)',
		'rating_asc'   => 'Рейтингу (возрастание)',
		'rating_desc'  => 'Рейтингу (убывание)',
	);
//	$this->sort = $this->getRequestParameter('sort', 'created_desc');
//	$this->page = $this->getRequestParameter('page', 1);
	$this->list = $this->product->getCommentList(array(
		'parent_id' => 0, 
		'page' => $this->page, 
		'sort' => $this->sort
	));
  }
 /**
  * Executes form component
  *
  * @param Product $product Товар
  * @param ProductCommentForm $form Форма комментария товара
  */
  public function executeForm()
  {
	  $this->ratingTypes = ProductRatingTypePropertyTable::getInstance()->findAll(Doctrine_Core::HYDRATE_ARRAY);
    if (empty($this->form))
    {
      $this->form = new ProductCommentForm();
    }
  }
}
