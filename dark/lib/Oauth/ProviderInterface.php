<?php

namespace Oauth;

interface ProviderInterface {
    public function getLoginUrl();
    public function getUser(\Http\Request $request);
}