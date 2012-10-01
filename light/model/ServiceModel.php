<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.06.12
 * Time: 17:19
 * To change this template use File | Settings | File Templates.
 */

require_once(Config::get('rootPath').'system/App.php');
require_once(Config::get('rootPath').'lib/TimeDebug.php');
require_once(Config::get('viewPath').'dataObject/ServiceData.php');
require_once(Config::get('rootPath').'lib/CurrentUser.php');

class ServiceModel
{

  /**
   * @param int[] $idList
   * @param $callback
   * @return ServiceData[]
   */
  public function getServicesByIdListAsync($idList, $callback) {
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);

    if(count($ids) < 1){
      $callback(array());
      return;
    }

    $cb = function($response) use (&$callback)
    {
      $list = array();
      foreach ($response as $item)
        $list[] = new ServiceData($item);
      $callback($list);
    };

    App::getCoreV2()->addQuery('service/get', array(
      'id' => $ids,
      'geo_id' => App::getCurrentUser()->getRegion()->getId(),
    ), array(), $cb);
  }

    public function getCategoryRootTree($max_depth){
        $params = array(
            'max_depth' => (int)$max_depth,
            'geo_id' => App::getCurrentUser()->getRegion()->getId(),
        );
        if(!is_null($max_depth)){
            $params['max_depth'] = (int)$max_depth;
        }

        $data = App::getCoreV2()->query('service/get-category-tree', $params);

        if(empty($data) || !is_array($data)){
            return null;
        }
        return $this->createCategoryEntity((array)$data);
    }

    /**
     * @param array $data
     * @return ServiceData
     */
    private function createCategoryEntity(array $data)
    {
        $category = new ServiceData($data);

        return $category;
    }

    public function getByToken($token)
    {
        $result = App::getCoreV2()->query('service/get2', array(
            'slug' => (string)$token,
            'geo_id' => App::getCurrentUser()->getRegion()->getId(),
        ));
        if (empty($result)) {
            return null;
        }
        return $this->createService((array)$result[0]);
    }

    public function getListById(array $idList)
    {
        if(empty($idList)){
            return array();
        }
        $result = App::getCoreV2()->query('service/get2', array(
            'id' => $idList,
            'geo_id' => App::getCurrentUser()->getRegion()->getId(),
        ));

        $list = array();
        if (is_array($result))
            foreach ($result as $serviceData)
                $list[] = $this->createService($serviceData);

        return $list;
    }

    private function createService(array $data)
    {
        $service = new ServiceData($data);

        return $service;
    }

    public function getCategoryTreeByToken($token, $max_depth=null){
        $params = array(
            'slug' => (string)$token,
            'geo_id' => App::getCurrentUser()->getRegion()->getId(),
        );
        if(!is_null($max_depth)){
            $params['max_depth'] = (int)$max_depth;
        }
        $data = App::getCoreV2()->query('service/get-category-tree', $params);
        if(empty($data) || !is_array($data)){
            return null;
        }

        return $this->createCategoryEntity((array)$data);
    }

    public function loadServiceList(array $categoryList){
        foreach($categoryList as $category){
          /** @var ServiceData $category  */
            App::getCoreV2()->addQuery('service/list', array(
                'category_id' => $category->getId(),
                'geo_id' => App::getCurrentUser()->getRegion()->getId(),
            ), array(), function($data) use($category){
                $category->setServiceIdList($data['list']);
            });
        }
        App::getCoreV2()->execute();
        $idList = array();
        foreach($categoryList as $category){
            $idList = array_merge($idList, $category->getServiceIdList());
        }
        if(!empty($idList)){
            $idList = array_unique($idList);
            $result = App::getCoreV2()->query('service/get2', array(
                'id' => $idList,
                'geo_id' => App::getCurrentUser()->getRegion()->getId(),
            ));
            $map = array();
            if (is_array($result))
                foreach ($result as $serviceData)
                    $map[$serviceData['id']] = $this->createService($serviceData);
            foreach($categoryList as $category){
                foreach($category->getServiceIdList() as $id){
                    if(isset($map[$id])){
                        $category->addService($map[$id]);
                    }
                }
            }
        }
    }

}
