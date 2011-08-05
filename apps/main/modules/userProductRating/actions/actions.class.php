<?php

/**
 * userProductRating actions.
 *
 * @package    enter
 * @subpackage userProductRating
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userProductRatingActions extends sfActions
{
 /**
  * Executes create action
  *
  * @param sfRequest $request A request object
  */
  public function executeCreate(sfWebRequest $request)
  {
    $user = $this->getUser();

    $this->redirectUnless($user->isAuthenticated(), '@user_signin');

    if (is_array($request['rating']))
    {
      $product = $this->getRoute()->getObject();
      $productRatingType = ProductRatingTypeTable::getInstance()->getById($product->Type->rating_type_id);
      foreach ($productRatingType->Property as $productRatingTypeProperty)
      {
        $value = isset($request['rating'][$productRatingTypeProperty->id]) ? (float)$request['rating'][$productRatingTypeProperty->id] : false;
        if (false !== $value)
        {
          $userProductRating = new UserProductRating();
          $userProductRating->fromArray(array(
            'user_id'     => $user->getGuardUser()->id,
            'property_id' => $productRatingTypeProperty->id,
            'product_id'  => $product->id,
            'value'       => $value,
          ));
          $userProductRating->replace();
        }
      }
    }

    $this->redirect($request->getReferer());
  }
}
