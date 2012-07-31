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
      $result = CoreClient::getInstance()->query('geo/autocomplete', array('letters' => $keyword));
      $i = 0;
      //dump($result, 1);
      foreach ($result as $item)
      {
        if ($i >= $limit) break;

        $data[] = array(
          //'token' => $item['token'],
          'name'  =>
            $item['name']
            .(
              (!empty($item['region']['name']) && ($item['name'] != $item['region']['name']))
              ? (" ({$item['region']['name']})")
              : ''
            ),
          'url'  => $this->generateUrl('region_change', array('region' => $item['id'])),
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
     if(!isset($request['region'])){
      $this->redirect($request->getReferer() ?: 'homepage');
      return;
    }
    $regionId = (int)$request['region'];

    if(!$regionId){
      $this->redirect($request->getReferer() ?: 'homepage');
      return;
    }

    $region = RepositoryManager::getRegion()->getById($regionId);

    if ($region)
    {
      $this->getUser()->setRegion($regionId);
    }

    $this->redirect($request->getReferer() ?: 'homepage');
  }

  public function executeRedirect(sfWebRequest $request)
  {
//    $region = RegionTable::getInstance()->getByToken($request['region']);

    $this->getUser()->setRegion(intval($request['region']));

    $newUrl = preg_replace('/\/reg\/.*?\//i', '/', $request->getUri());
    $this->redirect($newUrl);
  }

  public function executeInit(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $regions = RepositoryManager::getRegion()->getShopAvailable();

    $return = array();
    foreach ($regions as $region)
    {
      $item = array('name' => $region->getName(), 'link' => $this->generateUrl('region_change', array('region' => $region->getId())));
      if ($region->getId() == $this->getUser()->getRegion('id'))
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
