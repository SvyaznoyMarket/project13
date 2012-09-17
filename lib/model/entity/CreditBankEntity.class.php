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
   * Кредитный брокет Kupivkredit
   */
  const PROVIDER_KUPIVKREDIT = 1;

  /**
   * Кредитный брокет Direct Credit
   */
  const PROVIDER_DIRECT_CREDIT = 2;

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
  private $token;

  /**
   * @var string
   */
  private $href;

  /**
   * @var string
   */
  private $description;


    public function __construct(array $data = array()){
        if(array_key_exists('id', $data))      $this->id       = (int)$data['id'];
        if(array_key_exists('name', $data))   $this->name      = (string)$data['name'];
        if(array_key_exists('token', $data))   $this->token    = $data['token'];
        if(array_key_exists('href', $data))   $this->href    = $data['href'];
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

    /**
     * @param int $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param int $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return int
     */
    public function getHref()
    {
        return $this->href;
    }
}
