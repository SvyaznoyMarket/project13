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
    $user = $this->getUser();

    $list = range(1, 5, 0.5);

    if ($user->isAuthenticated())
    {
      //$hasRating = $user->hasProductRating($this->product->id);
      $userProductRating = $user->getGuardUser()->getProductRatingByProduct($this->product->id);

      $this->setVar('userValue', $userProductRating ? $userProductRating->value : null, true);
      $this->setVar('list', $list, true);
    }
  }
}
