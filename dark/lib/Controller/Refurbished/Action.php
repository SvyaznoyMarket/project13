<?php
namespace Controller\Refurbished;

class Action {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Refurbished\IndexPage();
        $form = new \View\Refurbished\SubscribeForm();

        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    public function subscribe(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException();
        }

        $response = array('success' => false);

        $form = new \View\Refurbished\SubscribeForm();
        $form->fromArray($request->request->get('subscriber'));

        $response['post_data'] = $_POST;

        if (!$form->getName()) {
            $form->setError('username', 'Не указан логин');
        }
        if (!$form->getEmail()) {
            $form->setError('email', 'Не указана почта');
        }


        if ($form->isValid()) {
            $response = array('success' => true);
            return new \Http\JsonResponse($response);
        }

        $response = array(
            'succsess' => $form->isValid(),
            'data'     => array(
                'content' => \App::templating()->render('form-login', array(
                    'page'    => new \View\Layout(),
                    'form'    => $form,
                    'request' => \App::request(),
                )),
            )
        );

        return new \Http\JsonResponse($response);

    }
}