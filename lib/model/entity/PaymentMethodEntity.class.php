<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Trushina
 * Date: 09.06.12
 * Time: 12:33
 * To change this template use File | Settings | File Templates.
 */
class PaymentMethodEntity
{
  /**
   * @var int
   */
  private $id;

  /**
   * @var int
   */
  private $is_credit;

  /**
   * @var int
   */
  private $is_online;

    /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $description;

  /**
   * @var array
   */
  private $credit_bank = array();


    public function __construct(array $data = array()){
        if(array_key_exists('id', $data))      $this->id       = (int)$data['id'];
        if(array_key_exists('name', $data))   $this->name      = (string)$data['name'];
        if(array_key_exists('description', $data))   $this->description    = $data['description'];
        if(array_key_exists('is_credit', $data))   $this->is_credit    = (float)$data['is_credit'];
        if(array_key_exists('is_online', $data))   $this->is_online    = (float)$data['is_online'];
        if(array_key_exists('credit_bank', $data)) {
           foreach ($data['credit_bank'] as $bankData) {
                $this->credit_bank[] = new CreditBankEntity($bankData);
           }
        }
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
   * @param float $price
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return float
   */
  public function getDescription()
  {
    return $this->description;
  }

    /**
     * @param string $name
     */
    public function setIsCredit($is_credit)
    {
        $this->is_credit = $is_credit;
    }

    /**
     * @return string
     */
    public function IsCredit()
    {
        return $this->is_credit;
    }

    public function isCertificate() {
        // заглушка
        return $this->id == 9;
    }

    /**
     * @param string $name
     */
    public function setIsOnline($is_online)
    {
        $this->is_online = $is_online;
    }

    /**
     * @return string
     */
    public function getIsOnline()
    {
        return $this->is_online;
    }

    /**
     * @param array $credit_bank
     */
    public function setCreditBank($credit_bank)
    {
        $this->credit_bank = $credit_bank;
    }

    /**
     * @return array
     */
    public function getCreditBank()
    {
        return $this->credit_bank;
    }

}
