<?php
namespace Controller\Refurbished;

class Action {

    private $channelId = 3;

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

        $response['post_data'] = $request->request->all();

        if (!$form->getName()) {
            $form->setError('name', 'Не указано имя');
        }
        if (!$form->getEmail()) {
            $form->setError('email', 'Не указана почта');
        }


        if ($form->isValid()) {
            try {
                $name = explode(' ', $form->getName());
                $response = \App::coreClientV2()->query('user/callback-create', [], array(
                    'channel_id' => $this->channelId,
                    'first_name' => isset($name[0]) ? $name[0] : null,
                    'last_name' =>  isset($name[1]) ? $name[1] : null,
                    'email' => $form->getEmail(),
                    'theme' => 'Подписка на уцененные товары',
                    'text' => $form->getName() . ' хочет получать списки уцененных товаров на адрес ' . $form->getEmail(),
                ));

                if (!isset($response['confirmed']) || !$response['confirmed']) {
                    throw new \Exception('Не удалось сохранить форму');
                }

                $response = array('success' => true);
                return new \Http\JsonResponse($response);
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);

                $form->setError('global', 'Не удалось сохранить форму');
            }

        }

        $response = array(
            'succsess' => $form->isValid(),
            'data'     => array(
                'content' => \App::templating()->render('refurbished/form-subscribe', array(
                    'page'    => new \View\Layout(),
                    'form'    => $form,
                    'request' => \App::request(),
                )),
            )
        );

        return new \Http\JsonResponse($response);

    }
}