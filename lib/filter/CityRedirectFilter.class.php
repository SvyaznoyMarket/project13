<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pavelkuznetsov
 * Date: 14.02.12
 * Time: 14:16
 * To change this template use File | Settings | File Templates.
 */
class CityRedirectFilter extends sfFilter{

  const PARAM_NAME = "city_id";

  public function execute ($filterChain)
  {
    $context = $this->getContext();
    $request = $context->getRequest();

    $cityId = $request->getGetParameter(self::PARAM_NAME, false); //если передается в посте - обработка не нужна

    if($cityId){
      $regionTable = RegionTable::getInstance();
      $region = $regionTable->getByToken($cityId);
    }
    else{
      $region = false;
    }

    if($region){
      $user = $context->getUser();
      $user->setRegion($region['id']);
      $newUrl = preg_replace('/\?'.self::PARAM_NAME.'=[-_0-9a-zA-Z]+\&/i', '?', $request->getUri());
      $newUrl = preg_replace('/\?'.self::PARAM_NAME.'=[-_0-9a-zA-Z]+/i', '', $newUrl);
      $newUrl = preg_replace('/\&'.self::PARAM_NAME.'=[-_0-9a-zA-Z]+/i', '', $newUrl);
      return $context->getController()->redirect($newUrl);
    }
    $filterChain->execute();
  }
}
