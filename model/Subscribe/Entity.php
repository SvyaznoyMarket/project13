<?php

namespace Model\Subscribe;

class Entity {
    /* @var int|null */
    private $channelId;
    /* @var string|null */
    private $type;
    /* @var string|null */
    private $email;
    /* @var bool|null */
    private $isConfirmed;

    public function __construct(array $data = []) {
        if (array_key_exists('channel_id', $data)) $this->setChannelId($data['channel_id']);
        if (array_key_exists('type', $data)) $this->setType($data['type']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('is_confirmed', $data)) $this->setIsConfirmed($data['is_confirmed']);
    }

    /**
     * @param int $channelId
     */
    public function setChannelId($channelId) {
        $this->channelId = (int)$channelId;
    }

    /**
     * @return int
     */
    public function getChannelId() {
        return $this->channelId;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param bool $isConfirmed
     */
    public function setIsConfirmed($isConfirmed) {
        $this->isConfirmed = (bool)$isConfirmed;
    }

    /**
     * @return bool
     */
    public function getIsConfirmed() {
        return $this->isConfirmed;
    }
}
