<?php

namespace View\User;

class CorporateRegistrationForm {
    /** @var string */
    private $firstName;
    /** @var string */
    private $middleName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $email;
    /** @var string */
    private $phone;

    /** @var string */
    private $corpForm;
    /** @var string */
    private $corpName;
    /** @var string */
    private $corpLegalAddress;
    /** @var string */
    private $corpRealAddress;
    /** @var string */
    private $corpINN;
    /** @var string */
    private $corpKPP;
    /** @var string Расчетный счет */
    private $corpAccount;
    /** @var string Корреспондентский счет */
    private $corpKorrAccount;
    /** @var string */
    private $corpBIK;
    /** @var string */
    private $corpOKPO;
    /** @var string */
    private $corpOKVED;
    /** @var string */
    private $corpEmail;
    /** @var string */
    private $corpPhone;
    private $errors = array(
        'global'             => null,
        'first_name'         => null,
        'middle_name'        => null,
        'last_name'          => null,
        'email'              => null,
        'phone'              => null,
        'corp_form'          => null,
        'corp_name'          => null,
        'corp_legal_address' => null,
        'corp_real_address'  => null,
        'corp_inn'           => null,
        'corp_kpp'           => null,
        'corp_account'       => null,
        'corp_korr_acount'   => null,
        'corp_bik'           => null,
        'corp_okpo'          => null,
        'corp_okved'         => null,
        'corp_email'         => null,
        'corp_phone'         => null,
        'corp_korr_account'  => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    public function fromArray(array $data) {
        if (array_key_exists('first_name', $data)) $this->setFirstName($data['first_name']);
        if (array_key_exists('middle_name', $data)) $this->setMiddleName($data['middle_name']);
        if (array_key_exists('last_name', $data)) $this->setLastName($data['last_name']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('phone', $data)) $this->setPhone($data['phone']);
        if (array_key_exists('corp_form', $data)) $this->setCorpForm($data['corp_form']);
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

    /**
     * @param string $corpAccount
     */
    public function setCorpAccount($corpAccount) {
        $this->corpAccount = trim((string)$corpAccount);
    }

    /**
     * @return string
     */
    public function getCorpAccount() {
        return $this->corpAccount;
    }

    /**
     * @param string $corpBIK
     */
    public function setCorpBIK($corpBIK) {
        $this->corpBIK = trim((string)$corpBIK);
    }

    /**
     * @return string
     */
    public function getCorpBIK() {
        return $this->corpBIK;
    }

    /**
     * @param string $corpEmail
     */
    public function setCorpEmail($corpEmail) {
        $this->corpEmail = trim((string)$corpEmail);
    }

    /**
     * @return string
     */
    public function getCorpEmail() {
        return $this->corpEmail;
    }

    /**
     * @param string $corpINN
     */
    public function setCorpINN($corpINN) {
        $this->corpINN = trim((string)$corpINN);
    }

    /**
     * @return string
     */
    public function getCorpINN() {
        return $this->corpINN;
    }

    /**
     * @param string $corpKPP
     */
    public function setCorpKPP($corpKPP) {
        $this->corpKPP = trim((string)$corpKPP);
    }

    /**
     * @return string
     */
    public function getCorpKPP() {
        return $this->corpKPP;
    }

    /**
     * @param string $corpKorrAccount
     */
    public function setCorpKorrAccount($corpKorrAccount) {
        $this->corpKorrAccount = trim((string)$corpKorrAccount);
    }

    /**
     * @return string
     */
    public function getCorpKorrAccount() {
        return $this->corpKorrAccount;
    }

    /**
     * @param string $corpLegalAddress
     */
    public function setCorpLegalAddress($corpLegalAddress) {
        $this->corpLegalAddress = trim((string)$corpLegalAddress);
    }

    /**
     * @return string
     */
    public function getCorpLegalAddress() {
        return $this->corpLegalAddress;
    }

    /**
     * @param string $corpName
     */
    public function setCorpName($corpName) {
        $this->corpName = trim((string)$corpName);
    }

    /**
     * @return string
     */
    public function getCorpName() {
        return $this->corpName;
    }

    /**
     * @param string $corpOKPO
     */
    public function setCorpOKPO($corpOKPO) {
        $this->corpOKPO = trim((string)$corpOKPO);
    }

    /**
     * @return string
     */
    public function getCorpOKPO() {
        return $this->corpOKPO;
    }

    /**
     * @param string $corpOKVED
     */
    public function setCorpOKVED($corpOKVED) {
        $this->corpOKVED = trim((string)$corpOKVED);
    }

    /**
     * @return string
     */
    public function getCorpOKVED() {
        return $this->corpOKVED;
    }

    /**
     * @param string $corpPhone
     */
    public function setCorpPhone($corpPhone) {
        $this->corpPhone = trim((string)$corpPhone);
    }

    /**
     * @return string
     */
    public function getCorpPhone() {
        return $this->corpPhone;
    }

    /**
     * @param string $corpRealAddress
     */
    public function setCorpRealAddress($corpRealAddress) {
        $this->corpRealAddress = trim((string)$corpRealAddress);
    }

    /**
     * @return string
     */
    public function getCorpRealAddress() {
        return $this->corpRealAddress;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = trim((string)$email);
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName) {
        $this->firstName = trim((string)$firstName);
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName) {
        $this->lastName = trim((string)$lastName);
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName) {
        $this->middleName = trim((string)$middleName);
    }

    /**
     * @return string
     */
    public function getMiddleName() {
        return $this->middleName;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = trim((string)$phone);
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * @param string $form
     */
    public function setCorpForm($form)
    {
        $this->corpForm = trim((string)$form);
    }

    /**
     * @return string
     */
    public function getCorpForm()
    {
        return $this->corpForm;
    }

    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    public function getCorpFormSelection() {
        return [
            'ИП' => 'Индивидуальный предприниматель (ИП)',
            'ООО' => 'Общество с ограниченной ответственностью (ООО)',
            'ОАО' => 'Открытое Акционерное общество (ОАО)',
            'ЗАО' => 'Закрытое Акционерное общество (ЗАО)',
        ];
    }
}