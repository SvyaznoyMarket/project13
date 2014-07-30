<?php


namespace Model\User;


class SubscriptionEntity {

    /**
     * @var int|null
     */
    private $channelId;
    /**
     * @var string|null
     */
    private $email;
    /** Статус подписки
     * @var bool|null
     */
    private $isConfirmed;
    /**
     * @var \Model\Subscribe\Channel\Entity|null
     */
    private $channel;
    /** Тип подписки (email, sms)
     * @var string|null
     */
    private $type;

    function __construct($data)
    {
        if (isset($data['channel_id'])) $this->setChannelId($data['channel_id']);
        if (isset($data['email'])) $this->setEmail($data['email']);
        if (isset($data['is_confirmed'])) $this->setIsConfirmed($data['is_confirmed']);
        if (isset($data['type'])) $this->setType($data['type']);
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * @param mixed $channelId
     */
    private function setChannelId($channelId)
    {
        $this->channelId = (int)$channelId;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    private function setEmail($email)
    {
        $this->email = (string)$email;
    }

    /**
     * @return mixed
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * @param mixed $isConfirmed
     */
    private function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = (bool)$isConfirmed;
    }

    /**
     * @return \Model\Subscribe\Channel\Entity|null
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param \Model\User\SubscriptionEntity|null $channel
     */
    public function setChannel($channel)
    {
        if ($channel instanceof \Model\Subscribe\Channel\Entity) $this->channel = $channel;
    }

    /**
     * @param mixed $type
     */
    private function setType($type)
    {
        $this->type = (string)$type;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

} 