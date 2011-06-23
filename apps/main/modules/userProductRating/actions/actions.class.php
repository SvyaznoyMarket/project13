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

    if ($request['value'])
    {
      $product = $this->getRoute()->getObject();

      $userProductRating = new UserProductRating();
      $userProductRating->fromArray(array(
        'user_id'    => $user->getGuardUser()->id,
        'product_id' => $product->id,
        'value'      => $request['value'],
      ));
      $userProductRating->replace();
    }

    $this->redirect($request->getReferer());
  }
}
