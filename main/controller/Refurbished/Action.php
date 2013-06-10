<?php

namespace Controller\Refurbished;

class Action {
    /**
     * @return \Http\Response
     */
    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Refurbished\IndexPage();
        $form = new \View\Refurbished\SubscribeForm();

        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function subscribe(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException();
        }

        $responseData = ['success' => false];

        $form = new \View\Refurbished\SubscribeForm();
        $form->fromArray($request->request->get('subscriber'));

        $responseData['post_data'] = $request->request->all();

        if (!$form->getEmail()) {
            $form->setError('email', 'Не указана почта');
        }

        if ($form->isValid()) {
            try {
                $params = [
                    'email'      => $form->getEmail(),
                    'channel_id' => 3,
                ];
                if ($userEntity = \App::user()->getEntity()) {
                    $params['token'] = $userEntity->getToken();
                }

                $exception = null;
                $client->addQuery('subscribe/create', $params, [], function($data) {}, function(\Exception $e) use(&$exception) {
                    $exception = $e;
                    \App::exception()->remove($e);
                });
                $client->execute();

                if ($exception instanceof \Exception) {
                    throw $exception;
                }

                $responseData = ['success' => true];

                return new \Http\JsonResponse($responseData);
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                $form->setError('global', 'Не удалось сохранить форму');
            }
        }

        $responseData = [
            'succsess' => $form->isValid(),
            'data'     => [
                'content' => \App::templating()->render('refurbished/form-subscribe', array(
                    'page'    => new \View\Layout(),
                    'form'    => $form,
                    'request' => \App::request(),
                )),
            ]
        ];

        return new \Http\JsonResponse($responseData);
    }
}