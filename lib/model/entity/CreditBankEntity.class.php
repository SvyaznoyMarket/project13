<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 09.06.12
 * Time: 12:33
 * To change this template use File | Settings | File Templates.
 */
class CreditBankEntity
{
  /**
   * @var int
   */
  private $id;

  /**
   * @var int
   */
  private $provider_id;

  /**
   * @var int
   */
  private $position;

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $description;


    public function __construct(array $data = array()){
        if(array_key_exists('id', $data))      $this->id       = (int)$data['id'];
        if(array_key_exists('name', $data))   $this->name      = (string)$data['name'];
        if(array_key_exists('description', $data))   $this->description    = $data['description'];
        if(array_key_exists('provider_id', $data))   $this->provider_id    = (float)$data['provider_id'];
        if(array_key_exists('position', $data))   $this->position    = (float)$data['position'];
    }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

    /**
     * @param int $provider_id
     */
    public function setProviderId($provider_id)
    {
        $this->provider_id = $provider_id;
    }

    /**
     * @return int
     */
    public function getProviderId()
    {
        return $this->provider_id;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

}
