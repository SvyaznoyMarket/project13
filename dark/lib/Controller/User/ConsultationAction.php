<?php

namespace Controller\User;

class ConsultationAction {

    public function __construct() {
        if (!\App::user()->getEntity()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();

        $userEntity = \App::user()->getEntity();

        $form = new \View\User\ConsultationForm();
        $form->setName($userEntity->getName());
        $form->setEmail($userEntity->getEmail());

        $message = $session->get('flash');
        $session->remove('flash');

        $error = null;

        if ($request->isMethod('post')) {
            $form->fromArray($request->request->get('form'));

            try {
                if (
                    !$form->getName()
                    || !$form->getEmail()
                    || !$form->getSubject()
                    || !$form->getMessage()
                ) {
                    $error = 'Проверьте правильность заполнения полей формы';
                    throw new \Exception($error);
                }

                $response = \App::coreClientV2()->query('user/callback-create', array(), array(
                    'token' => $userEntity->getToken(),
                ));

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить форму');
                }

                $session->set('flash', 'Сообщение отправлено');

                return new \Http\RedirectResponse(\App::router()->generate('user.consultation'));
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                $error = $error ?: 'Не удалось отправить сообщение. Попробуйте позже';
            }
        }

        $page = new \View\User\ConsultationPage();
        $page->setParam('form', $form);
        $page->setParam('message', $message);
        $page->setParam('error', $error);

        return new \Http\Response($page->show());
    }
}