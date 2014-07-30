<?php


namespace Model\User\Order;


class LifecycleEntity {

    /**
     * @var int|null
     */
    private $statusId;

    /**
     * @var bool|null
     */
    private $completed;

    /**
     * @var string|null
     */
    private $title;

    function __construct($data)
    {
        if (isset($data['status_id'])) $this->setStatusId($data['status_id']);
        if (isset($data['completed'])) $this->setCompleted($data['completed']);
        if (isset($data['title']))  $this->setTitle($data['title']);
    }

    /**
     * @param mixed $completed
     */
    private function setCompleted($completed)
    {
        $this->completed = (bool)$completed;
    }

    /**
     * @return bool|null
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $statusId
     */
    private function setStatusId($statusId)
    {
        $this->statusId = (int)$statusId;
    }

    /**
     * @return int|null
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param mixed $title
     */
    private function setTitle($title)
    {
        $this->title = (string)$title;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

} 