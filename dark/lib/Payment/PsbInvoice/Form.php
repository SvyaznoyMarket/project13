<?php

namespace Payment\PsbInvoice;

class Form {
    /** @var string */
    private $contractorId;
    /** @var string */
    private $invoiceId;
    /** @var string */
    private $sum;
    /** @var string */
    private $payDescription;
    /** @var string */
    private $additionalInfo;
    /** @var string */
    private $signature;

    /**
     * @param array $data
     */
    public function fromArray(array $data) {
        if (array_key_exists('ContractorID', $data)) $this->setContractorId($data['ContractorID']);
        if (array_key_exists('InvoiceID', $data)) $this->setInvoiceId($data['InvoiceID']);
        if (array_key_exists('Sum', $data)) $this->setSum($data['Sum']);
        if (array_key_exists('PayDescription', $data)) $this->setPayDescription($data['PayDescription']);
        if (array_key_exists('AdditionalInfo', $data)) $this->setAdditionalInfo($data['AdditionalInfo']);
        if (array_key_exists('Signature', $data)) $this->setSignature($data['Signature']);
    }

    /**
     * @param string $additionalInfo
     */
    public function setAdditionalInfo($additionalInfo) {
        $this->additionalInfo = (string)$additionalInfo;
    }

    /**
     * @return string
     */
    public function getAdditionalInfo() {
        return $this->additionalInfo;
    }

    /**
     * @param string $contractorId
     */
    public function setContractorId($contractorId) {
        $this->contractorId = (string)$contractorId;
    }

    /**
     * @return string
     */
    public function getContractorId() {
        return $this->contractorId;
    }

    /**
     * @param string $invoiceId
     */
    public function setInvoiceId($invoiceId) {
        $this->invoiceId = (string)$invoiceId;
    }

    /**
     * @return string
     */
    public function getInvoiceId() {
        return $this->invoiceId;
    }

    /**
     * @param string $payDescription
     */
    public function setPayDescription($payDescription) {
        $this->payDescription = (string)$payDescription;
    }

    /**
     * @return string
     */
    public function getPayDescription() {
        return $this->payDescription;
    }

    /**
     * @param string $signature
     */
    public function setSignature($signature) {
        $this->signature = (string)$signature;
    }

    /**
     * @return string
     */
    public function getSignature() {
        return $this->signature;
    }

    /**
     * @param string $sum
     */
    public function setSum($sum) {
        $this->sum = (string)$sum;
    }

    /**
     * @return string
     */
    public function getSum() {
        return $this->sum;
    }
}