<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.06.12
 * Time: 17:19
 * To change this template use File | Settings | File Templates.
 */

require_once(ROOT_PATH.'system/App.php');
require_once(ROOT_PATH.'lib/TimeDebug.php');
require_once(VIEW_PATH.'dataObject/ServiceData.php');

class ServiceModel
{

  public function getServicesByIdListAsync($idList, $callback){
    $ids = array();
    foreach($idList as $id){
      $ids[] = (int) $id;
    }
    $ids = array_unique($ids);

    if(count($ids) < 1){
      $callback(array());
      return;
    }

    $list = array();

//    if(array_key_exists('id', $data)){ $this->setId((int) $data['id']); }
//    if(array_key_exists('name', $data)){ $this->setName((string) $data['name']); }
//    if(array_key_exists('token', $data)){ $this->setToken((string) $data['token']); }
//    if(array_key_exists('description', $data)){ $this->setDescription((string) $data['description']); }
//    if(array_key_exists('work', $data)){ $this->setWork((string) $data['work']); }
//    if(array_key_exists('media_image', $data)){ $this->setMediaImage((string) $data['media_image']); }
//    if(array_key_exists('is_active', $data)){ $this->setIsActive((bool) $data['is_active']); }
//    if(array_key_exists('only_inshop', $data)){ $this->setOnlyInShop((bool) $data['only_inshop']); }
//    if(array_key_exists('category', $data)){ $this->setCategory($data['category']); }
//    if(array_key_exists('alike', $data)){ $this->setAlike($data['alike']); }

    $services = array(
      array(
        'id' => 1,
        'name' => 'test1',
        'token' => 'test1',
        'description' => 'description for test1 service',
        'work' => 'work for test1 service',
        'media_image' => '/img/test1.jpg',
        'is_active' => 1,
        'only_inshop' => 0,
        'category_list' => array(
          array(
            "id" => 305,
            "parent_id"=> 310,
            "is_active"=> 1,
            "link"=> "http://wefewfwef",
            "token"=> "ergergergre",
            "name"=> "Мебель",
            "media_image"=> null,
            "position"=> 3,
            "level"=> 2
                ),
          array(
            "id"=> 147,
            "parent_id"=> 305,
            "is_active"=> 1,
            "link"=> "http://wefewfwef",
            "token"=> "ergergergre",
            "name"=> "Услуги по сборке и установке корпусной мебели",
            "media_image"=> null,
            "position"=> 1,
            "level"=> 3
          ),
          array(
            "id"=> 350,
            "parent_id"=> 147,
            "is_active"=> 1,
            "link"=> "http://wefewfwef",
            "token"=> "ergergergre",
            "name"=> "Услуги по сборке корпусной мебели",
            "media_image"=> null,
            "position"=> 1,
            "level"=> 4
           )
        ),
        'alike_list' => ''
      )
    );


    $callback($list);
  }

}
