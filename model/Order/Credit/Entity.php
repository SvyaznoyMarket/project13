<?php

namespace Model\Order\Credit;

class Entity {
    /** @var int */
    private $bankId;
    /** @var int */
    private $bankProviderId;
    /** @var int */
    private $sum;
    /** @var string */
    private $status;
    /** @var \DateTime|null */
    private $updatedAt;

    public function __construct(array $data = []) {
        if (array_key_exists('credit_bank_id', $data)) $this->setBankId($data['credit_bank_id']);
        if (array_key_exists('credit_provider_id', $data)) $this->setBankProviderId($data['credit_provider_id']);
        if (array_key_exists('credit_sum', $data)) $this->setSum($data['credit_sum']);
        if (array_key_exists('status', $data)) $this->setStatus($data['status']);
        if (array_key_exists('updated', $data) && $data['updated'] && ('0000-00-00' != $data['updated'])) {
            try {
                $this->setUpdatedAt(new \DateTime($data['updated']));
            } catch(\Exception $e) {
                \App::logger()->error($e);
            }
        }
    }

    /**
     * @param int $bankId
     */
    public function setBankId($bankId) {
        $this->bankId = (int)$bankId;
    }

    /**
     * @return int
     */
    public function getBankId() {
        return $this->bankId;
    }

    /**
     * @param int $bankProviderId
     */
    public function setBankProviderId($bankProviderId) {
        $this->bankProviderId = (int)$bankProviderId;
    }

    /**
     * @return int
     */
    public function getBankProviderId() {
        return $this->bankProviderId;
    }

    /**
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = (string)$status;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param int $sum
     */
    public function setSum($sum) {
        $this->sum = (int)$sum;
    }

    /**
     * @return int
     */
    public function getSum() {
        return $this->sum;
    }

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
}