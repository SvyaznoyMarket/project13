<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavelkuznetsov
 * Date: 14.02.12
 * Time: 14:16
 * To change this template use File | Settings | File Templates.
 */
class CityRedirectFilter extends sfFilter{

  public function execute ($filterChain)
  {
    $context = $this->getContext();
    $request = $context->getRequest();
    $response = $context->getResponse();


    $geoshop = $request->getCookie('geoshop', false);
    $isGeoshopChanged = $request->getCookie('geoshop_change', false);

    if($isGeoshopChanged === false && $geoshop !== false){
      $response->setCookie('geoshop', '');
      $response->setCookie('geoshop_change', 'yes');

      return $context->getController()->redirect($request->getUri());
    }
    $filterChain->execute();
  }
}
