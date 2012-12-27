<?php

namespace Controller\User;

class ExternalLoginResponseAction {
    public function execute($providerName, \Http\Request $request) {
        $user = \App::user();

        // если пользователь уже аутентифицирован
        if ($user->getEntity()) {
            return new \Http\RedirectResponse(\App::router()->generate('user'));
        }

        try {
            $provider = \App::oauth($providerName);
        } catch (\Exception $e) {
            throw new \Exception\NotFoundException(sprintf('Не найден провайдер аутентификации "%s"', $providerName));
        }

        try {
            $profile = $provider->getUser($request);
            if (!$profile instanceof \Oauth\Model\EntityInterface) {
                throw new \Exception('Не получен профайл пользователя');
            }
            if (!$profile->getId()) {
                throw new \Exception('У профайла не установлен id');
            }

            //TODO: отправить в ядро

            $response = new \Http\RedirectResponse(\App::router()->generate('user'));
            //$user->signIn($user, $response);

            return $response;
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $page = new \View\User\ExternalLoginResponsePage();
        $page->setParam('error', 'Неудачная попытка авторизации');

        return new \Http\Response($page->show());
    }
}
