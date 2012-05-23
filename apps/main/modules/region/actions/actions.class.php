<?php

/**
 * region actions.
 *
 * @package    enter
 * @subpackage region
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class regionActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }
 /**
  * Executes autocomplete action
  *
  * @param sfRequest $request A request object
  */
  public function executeAutocomplete(sfWebRequest $request)
  {
    $limit = 8;

    $this->forward404Unless($request->isXmlHttpRequest());

    $keyword = $request['q'];

    $data = array();
    if (mb_strlen($keyword) >= 3)
    {
      $result = CoreClient::getInstance()->query('GEO/autocomplete', array('letters' => $keyword));
      $i = 0;
      foreach ($result as $item)
      {
        if ($i >= $limit) break;

        $data[] = array(
          'token' => $item['token'],
          'name'  =>
            $item['name']
            .(
              (!empty($item['region']['name']) && ($item['name'] != $item['region']['name']))
              ? (" ({$item['region']['name']})")
              : ''
            ),
          'url'  => $this->generateUrl('region_change', array('region' => $item['token'])),
        );

        $i++;
      }
    }

    return $this->renderJson(array(
      'data' => $data,
    ));
  }

  public function executeChange(sfWebRequest $request)
  {
    if (intval($request['region']) == $request['region'])
    {
      $region = RegionTable::getInstance()->getByCoreId($request['region']);
    }
    else {
      $region = $this->getRoute()->getObject();
    }

    if ($region)
    {
      $this->getUser()->setRegion($region->id);
      $this->getUser()->setRegionCookie();
    }

    $this->redirect($request->getReferer() ?: 'homepage');
  }

  public function executeRedirect(sfWebRequest $request)
  {
//    $region = RegionTable::getInstance()->getByToken($request['region']);
    $region = RegionTable::getInstance()->findOneBy('core_id', intval($request['region']));

    if ($region)
    {
      $this->getUser()->setRegion($region->id);
      $this->getUser()->setRegionCookie();
    }

    $newUrl = preg_replace('/\/reg\/.*?\//i', '/', $request->getUri());
    $this->redirect($newUrl);
  }

  public function executeInit(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $user_region = $this->getUser()->getRegion();

    $regions = RegionTable::getInstance()->getListHavingShops();

    $return = array();
    foreach ($regions as $region)
    {
      $item = array('name' => $region->name, 'link' => $this->generateUrl('region_change', array('region' => $region->token, )), );
      if ($region->id == $user_region['id'])
      {
        $item['is_active'] = 'active';
      }
      $return[] = $item;
    }

    return $this->renderJson(array(
      'success' => true,
      'data'    => $return,
    ));
  }
}
