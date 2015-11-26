<?php

namespace Model\User;

class SubscriptionEntity {
    /**
     * @var int|null
     */
    public $channelId;
    /**
     * @var string|null
     */
    public $email;
    /** Статус подписки
     * @var bool|null
     */
    public $isConfirmed;
    /**
     * @var \Model\Subscribe\Channel\Entity|null
     */
    public $channel;
    /**
     * Тип подписки (email, sms)
     * @var string|null
     */
    public $type;

    /**
     * @param $data
     */
    function __construct($data) {
        if (isset($data['channel_id'])) $this->channelId = (int)$data['channel_id'];
        if (isset($data['email'])) $this->email = (string)$data['email'];
        if (isset($data['is_confirmed'])) $this->isConfirmed = $data['is_confirmed'];
        if (isset($data['type'])) $this->type = (string)$data['type'];
    }
}