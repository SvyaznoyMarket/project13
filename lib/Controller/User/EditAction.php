<?php

namespace Controller\User;

class EditAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $session = \App::session();

        $userEntity = \App::user()->getEntity();

        $form = new \View\User\EditForm();
        $form->fromEntity($userEntity);

        $message = $session->get('flash');
        $session->remove('flash');

        if ($request->isMethod('post')) {
            $form->fromArray($request->request->get('user'));

            try {
                $response = \App::coreClientV2()->query('user/update', array('token' => \App::user()->getToken()), array(
                    'first_name'  => $form->getFirstName(),
                    'middle_name' => $form->getMiddleName(),
                    'last_name'   => $form->getLastName(),
                    'sex'         => $form->getSex(),
                    'email'       => $form->getEmail(),
                    'mobile'      => $form->getMobilePhone(),
                    'phone'       => $form->getHomePhone(),
                    'skype'       => $form->getSkype(),
                    'birthday'    => $form->getBirthday() ? $form->getBirthday()->format('Y-m-d') : null,
                    'occupation'  => $form->getOccupation(),
                ));

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить форму');
                }

                $session->set('flash', 'Данные сохранены');

                return new \Http\RedirectResponse(\App::router()->generate('user.edit'));
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                $form->setError('global', 'Не удалось сохранить форму');
            }
        }

        $page = new \View\User\EditPage();
        $page->setParam('form', $form);
        $page->setParam('message', $message);

        return new \Http\Response($page->show());
    }
}