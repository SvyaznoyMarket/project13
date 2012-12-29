<?php

namespace Controller\User;

class ConsultationAction {

    private $channelId = 3;

    public function __construct() {
        if (!\App::user()->getEntity()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $userEntity = \App::user()->getEntity();

        $form = new \View\User\ConsultationForm();

        if ($request->isMethod('post')) {
            $form->fromArray($request->request->get('form'));

            try {
                $response = \App::coreClientV2()->query('user/callback-create', array(), array(
                    'token' => $userEntity->getToken(),
                ));

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить форму');
                }

                return new \Http\RedirectResponse(\App::router()->generate('user.edit'));
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                $form->setError('global', 'Не удалось сохранить форму');
            }
        }

        $page = new \View\User\ConsultationPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}