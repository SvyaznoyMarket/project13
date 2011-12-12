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
    $this->forward404Unless($request->isXmlHttpRequest());

    $keyword = $request['q'];
    $limit = $request['limit'] > 100 ? 100 : $request['limit'];
    $region_type = $request['type'];

    $q = RegionTable::getInstance()
      ->createBaseQuery()
      ->addWhere('region.type = ?', $region_type)
      ->addWhere('region.name LIKE ?', "%$keyword%")
    ;

    $list = array();
    foreach ($q->execute() as $region) {
      $parent = $region->getNode()->getParent();

      $list[] = array(
        'id'   => $region['id'],
        'name' => $region['name'].(($parent && $parent->level > 0) ? ', '.$parent->name : ''),
      );
    }

    return $this->renderJson(array(
      'data' => $list,
    ));
  }
  
  public function executeChange(sfWebRequest $request)
  {
    $region = $this->getRoute()->getObject();
    
    if ($region)
    {
      $this->getUser()->setRegion($region->id);
    }
    
    $this->redirect($request->getReferer());
  }
}
