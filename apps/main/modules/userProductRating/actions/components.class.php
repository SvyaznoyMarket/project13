<?php

/**
 * userProductRating components.
 *
 * @package    enter
 * @subpackage userProductRating
 * @author     Связной Маркет
 * @version    SVN: $Id: components.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductRatingComponents extends myComponents
{
 /**
  * Executes show component
  *
  * @param Product $product Товар
  *
  */
  public function executeShow()
  {

  }
 /**
  * Executes form component
  *
  * @param Product $product Товар
  *
  */
  public function executeForm()
  {
    $user = $this->getUser();

    $userProductRatingList =
      $user->isAuthenticated()
      ? $user->getGuardUser()->getProductRatingByProduct($this->product)
      : UserProductRatingTable::getInstance()->createList()
    ;
    $userProductRatingList->indexBy('property_id');

    $list = array();

    $productRatingType = ProductRatingTypeTable::getInstance()->getById($this->product->Type->rating_type_id);
    foreach ($productRatingType->Property as $productRatingTypeProperty)
    {
      $item = array(
        'name'   => (string)$productRatingTypeProperty,
        'ratings' => array(),
      );
      foreach (range(1, 5, 0.5) as $i => $rating)
      {
        $userProductRating = $userProductRatingList->getByIndex('property_id', $productRatingTypeProperty->id);
        $item['ratings'][] = array(
          'property_id' => $productRatingTypeProperty->id,
          'id'          => "rating-{$this->product->id}-{$productRatingTypeProperty->id}-{$i}",
          'value'       => $rating,
          'selected'    => $userProductRating && ($userProductRating->value == $rating),
        );
      }

      $list[] = $item;
    }

    $this->setVar('list', $list, true);
  }
}
