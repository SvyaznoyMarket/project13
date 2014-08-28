<?php

namespace Oauth\Model;

interface EntityInterface {
    public function getId();
    public function getFirstName();
    public function getLastName();
    public function setAccessToken($accessToken);
    public function getAccessToken();
}
