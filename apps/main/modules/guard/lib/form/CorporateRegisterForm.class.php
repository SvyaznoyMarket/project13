<?php

class CorporateRegisterForm {
  private $errors = array();

  private $firstName;
  private $middleName;
  private $lastName;
  private $email;
  private $phone;

  private $corpName;
  private $corpLegalAddress;
  private $corpRealAddress;
  private $corpINN;
  private $corpKPP;
  /** @var string Расчетный счет */
  private $corpAccount;
  /** @var string Корреспондентский счет */
  private $corpKorrAccount;
  private $corpBIK;
  private $corpOKPO;
  private $corpOKVED;
  private $corpEmail;
  private $corpPhone;

  public function __construct(array $data = array()) {
    $this->import($data);
  }

  public function import(array $data) {
    if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
    if (array_key_exists('middle_name', $data)) $this->setMiddleName($data['middle_name']);
    if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
    if (array_key_exists('email', $data)) $this->setEmail($data['email']);
    if (array_key_exists('phone', $data)) $this->setPhone($data['phone']);
    if (array_key_exists('corp_name', $data)) $this->setCorpName($data['corp_name']);
    if (array_key_exists('corp_legal_address', $data)) $this->setCorpLegalAddress($data['corp_legal_address']);
    if (array_key_exists('corp_real_address', $data)) $this->setCorpRealAddress($data['corp_real_address']);
    if (array_key_exists('corp_inn', $data)) $this->setCorpINN($data['corp_inn']);
    if (array_key_exists('corp_kpp', $data)) $this->setCorpKPP($data['corp_kpp']);
    if (array_key_exists('corp_account', $data)) $this->setCorpAccount($data['corp_account']);
    if (array_key_exists('corp_korr_acount', $data)) $this->setCorpKorrAccount($data['corp_korr_acount']);
    if (array_key_exists('corp_bik', $data)) $this->setCorpBIK($data['corp_bik']);
    if (array_key_exists('corp_okpo', $data)) $this->setCorpOKPO($data['corp_okpo']);
    if (array_key_exists('corp_okved', $data)) $this->setCorpOKVED($data['corp_okved']);
    if (array_key_exists('corp_email', $data)) $this->setCorpEmail($data['corp_email']);
    if (array_key_exists('corp_phone', $data)) $this->setCorpPhone($data['corp_phone']);
  }

  public function getErrors() {
    return $this->errors;
  }

  public function getError($name) {
    return array_key_exists($name, $this->errors) ? $this->errors[$name] : null;
  }

  /**
   * @param string $corpAccount
   */
  public function setCorpAccount($corpAccount)
  {
    $this->corpAccount = (string)$corpAccount;
  }

  /**
   * @return string
   */
  public function getCorpAccount()
  {
    return $this->corpAccount;
  }

  public function setCorpBIK($corpBIK)
  {
    $this->corpBIK = (string)$corpBIK;
  }

  public function getCorpBIK()
  {
    return $this->corpBIK;
  }

  public function setCorpEmail($corpEmail)
  {
    $this->corpEmail = (string)$corpEmail;
  }

  public function getCorpEmail()
  {
    return $this->corpEmail;
  }

  public function setCorpINN($corpINN)
  {
    $this->corpINN = (string)$corpINN;
  }

  public function getCorpINN()
  {
    return $this->corpINN;
  }

  public function setCorpKPP($corpKPP)
  {
    $this->corpKPP = (string)$corpKPP;
  }

  public function getCorpKPP()
  {
    return $this->corpKPP;
  }

  /**
   * @param string $corpKorrAccount
   */
  public function setCorpKorrAccount($corpKorrAccount)
  {
    $this->corpKorrAccount = (string)$corpKorrAccount;
  }

  /**
   * @return string
   */
  public function getCorpKorrAccount()
  {
    return $this->corpKorrAccount;
  }

  public function setCorpLegalAddress($corpLegalAddress)
  {
    $this->corpLegalAddress = (string)$corpLegalAddress;
  }

  public function getCorpLegalAddress()
  {
    return $this->corpLegalAddress;
  }

  public function setCorpName($corpName)
  {
    $this->corpName = (string)$corpName;
  }

  public function getCorpName()
  {
    return $this->corpName;
  }

  public function setCorpOKPO($corpOKPO)
  {
    $this->corpOKPO = (string)$corpOKPO;
  }

  public function getCorpOKPO()
  {
    return $this->corpOKPO;
  }

  public function setCorpOKVED($corpOKVED)
  {
    $this->corpOKVED = (string)$corpOKVED;
  }

  public function getCorpOKVED()
  {
    return $this->corpOKVED;
  }

  public function setCorpPhone($corpPhone)
  {
    $this->corpPhone = (string)$corpPhone;
  }

  public function getCorpPhone()
  {
    return $this->corpPhone;
  }

  public function setCorpRealAddress($corpRealAddress)
  {
    $this->corpRealAddress = (string)$corpRealAddress;
  }

  public function getCorpRealAddress()
  {
    return $this->corpRealAddress;
  }

  public function setEmail($email)
  {
    $this->email = (string)$email;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function setFirstName($firstName)
  {
    $this->firstName = (string)$firstName;
  }

  public function getFirstName()
  {
    return $this->firstName;
  }

  public function setLastName($lastName)
  {
    $this->lastName = (string)$lastName;
  }

  public function getLastName()
  {
    return $this->lastName;
  }

  public function setMiddleName($middleName)
  {
    $this->middleName = (string)$middleName;
  }

  public function getMiddleName()
  {
    return $this->middleName;
  }

  public function setPhone($phone)
  {
    $this->phone = (string)$phone;
  }

  public function getPhone()
  {
    return $this->phone;
  }
  
  public function validate() {
    if (!$this->getFirstName()) {
      $this->errors['first_name'] = 'Укажите имя';
    }
    if (!$this->getMiddleName()) {
      $this->errors['middle_name'] = 'Укажите отчество';
    }
    if (!$this->getLastName()) {
      $this->errors['last_name'] = 'Укажите фамилию';
    }
    if (!$this->getEmail()) {
      $this->errors['email'] = 'Укажите email';
    }
    if (!$this->getPhone()) {
      $this->errors['phone'] = 'Укажите номер телефона';
    }
    if (!$this->getCorpName()) {
      $this->errors['corp_name'] = 'Укажите название организации';
    }
    if (!$this->getCorpLegalAddress()) {
      $this->errors['corp_legal_address'] = 'Укажите юридический адрес';
    }
    if (!$this->getCorpRealAddress()) {
      $this->errors['corp_real_address'] = 'Укажите фактический адрес';
    }
    if (!$this->getCorpINN()) {
      $this->errors['corp_inn'] = 'Укажите ИНН';
    }
    if (!$this->getCorpKPP()) {
      $this->errors['corp_kpp'] = 'Укажите КПП';
    }
    if (!$this->getCorpAccount()) {
      $this->errors['corp_account'] = 'Укажите расчетный счет';
    }
    if (!$this->getCorpKorrAccount()) {
      $this->errors['corp_korr_account'] = 'Укажите корреспондентский счет';
    }
    if (!$this->getCorpBIK()) {
      $this->errors['corp_bik'] = 'Укажите БИК';
    }
    if (!$this->getCorpOKPO()) {
      $this->errors['corp_okpo'] = 'Укажите ОКПО';
    }

    return !(bool)$this->errors;
  }
}