<?php

namespace Enter\Exception;

class PermanentlyRedirect extends \Exception {
    /** @var string */
    protected $link;

    /**
     * @param mixed $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getLink() {
        return $this->link;
    }
}