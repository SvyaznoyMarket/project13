<?php

namespace Controller\User;

class ConsultationAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        $userEntity = \App::user()->getEntity();

        $form = new \View\User\ConsultationForm();

        if ($request->isMethod('post')) {
            $form->fromArray($request->request->get('form'));

            try {
                $response = \App::coreClientV1()->query('user.callback.create', array(), array(
                    // TODO: сделать
                ));

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить форму');
                }

                return new \Http\RedirectResponse(\App::router()->generate('user.edit'));
            } catch (\Exception $e) {
                $form->setError('global', 'Не удалось сохранить форму');
                \App::logger()->error($e);
            }
        }

        $page = new \View\User\ConsultationPage();
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }
}