<?php

namespace Controller\Mobidengi;

class IndexAction {
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if ($request->isXmlHttpRequest()) {
            $response = null;
            $phoneFromRequest = $request->request->get('phone');
            try {
                if (!preg_match('/\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}/', $phoneFromRequest)) throw new \Exception('Номер не прошел валидацию');

                $phone = preg_replace('/[-\(\) ]/', '', str_replace('+7', '8', $phoneFromRequest));
                $coreResponse = \App::coreClientV2()->query('coupon/mobi-dengi', [], ['mobile' => $phone]);

                if (isset($coreResponse['code']) && $coreResponse['code'] == 200) {
                    $html = (new \Helper\TemplateHelper())->render('mobidengi/_popup',['code' => 200, 'message' => 'Купон отправлен']);
                } else {
                    $html = (new \Helper\TemplateHelper())->render('mobidengi/_popup',['code' => 400, 'message' => 'Неизвестный ответ от сервера']);
                }
                $response = new \Http\JsonResponse(['result' => $html]);
            } catch (\RuntimeException $e) {
                // CURL exceptions
                \App::exception()->remove($e);
                switch ($e->getCode()) {
                    case 402: $message = '<b>У вас пока нет карты</b>, но вы можете оформить ее за 1 минуту и получить подарок'; break;
                    case 406: $message = 'К сожалению, все купоны закончились'; break;
                    case 409: $message = 'Купон уже был отправлен'; break;
                    case 600: $message = 'Ошибка валидации'; break;
                    default: $message = 'Возникла ошибка, попробуйте позднее';
                }

                $html = (new \Helper\TemplateHelper())->render('mobidengi/_popup', ['code' => $e->getCode(), 'message' => $message]);
                $response = new \Http\JsonResponse(['result' => $html]);
            } catch (\Exception $e) {
                $response = new \Http\JsonResponse(['error' => ['code' => $e->getCode()], 'message' => $e->getMessage()], 500);
            }
            return $response;
        }

        $page = new \View\Mobidengi\IndexPage();

        return new \Http\Response($page->show());
    }
}
