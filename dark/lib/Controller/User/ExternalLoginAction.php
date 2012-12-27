<?php

namespace Controller\User;

class ExternalLoginAction {
    /**
     * @param string        $providerName
     * @param \Http\Request $request
     * @return \Http\RedirectResponse
     * @throws \Exception\NotFoundException
     */
    public function execute($providerName, \Http\Request $request) {
        $user = \App::user();

        // TODO: как действовать?
        // если пользователь уже аутентифицирован
        if ($user->getEntity()) {
            return new \Http\RedirectResponse(\App::router()->generate('user'));
        }

        try {
            $provider = \App::oauth($providerName);
        } catch (\Exception $e) {
            throw new \Exception\NotFoundException(sprintf('Не найден провайдер аутентификации "%s"', $providerName));
        }

        return new \Http\RedirectResponse($provider->getLoginUrl());
    }
}
