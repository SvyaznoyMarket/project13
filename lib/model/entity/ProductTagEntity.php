<?php

class ProductTagEntity
{
  private $id;
  private $name;
  private $token;

  public function __construct(array $data = array())
  {
    if(array_key_exists('id', $data)) $this->id = (int)$data['id'];
    if(array_key_exists('name', $data)) $this->name = (string)$data['name'];
    if(array_key_exists('token', $data)) $this->token = (string)$data['token'];
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setToken($token)
  {
    $this->token = $token;
  }

  public function getToken()
  {
    return $this->token;
  }

  public function getUrl()
  {
    return sfContext::getInstance()->getRouting()->generate('tag_show', array('tag' => $this->token));
  }

  public function getSiteUrl()
  {
    $cache = myCache::getInstance();
    $key = __CLASS__.':id'.$this->id;
    if(!($token = $cache->get($key))){
      /** @var $tag Tag */
      $tag = TagTable::getInstance()->getByCoreId($this->id);
      $token = $tag->token;
      $cache->set($key, $token);
      $cache->addTag('tag-'.$tag->id, $key);
    }
    return sfContext::getInstance()->getRouting()->generate('tag_show', array('tag' => $token));
  }
}
