<?php

namespace Controller\Product;

class ReviewsAction {

    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\JsonResponse
     */
    /*public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = $request->get('page', 0);
        $reviewsType = $request->get('type', 'user');
        $layout = $request->get('layout', false);

        $reviewsData = \RepositoryManager::review()->getReviews($productId, $reviewsType, $page);

        $response = $reviewsType == 'user' ? 'Нет отзывов' : 'Нет обзоров';

        if(!empty($reviewsData['review_list'])) {
            $response = '';
            foreach ($reviewsData['review_list'] as $key => $review) {
                $response .= \App::templating()->render('product/_review', [
                    'page' => (new \View\Product\IndexPage()),
                    'review' => $review,
                    'last' => empty($reviewsData['review_list'][$key + 1]),
                    'layout' => $layout
                ]);
            }
        }

        return new \Http\JsonResponse(['content' => $response, 'pageCount' => empty($reviewsData['page_count']) ? 0 : $reviewsData['page_count']]);
    }*/


    /**
     * @param \Http\Request $request
     * @param int $productId
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $form = new \View\Product\ReviewForm();
        if ($request->isMethod('post')) {
            $form->fromArray((array)$request->get('review'));
            $form->setScore(10);
            $form->setDate(new \DateTime());
            $form->setProductId($productId);

            if (!$form->getPros()) {
                $form->setError('pros', 'Не указаны достоинства');
            }
            if (!$form->getCons()) {
                $form->setError('cons', 'Не указаны недостатки');
            }
            if (!$form->getExtract()) {
                $form->setError('extract', 'Не указан Комментарий');
            }
//            if (!$form->getScore()) {
//                $form->setError('score', 'Не указана оценка');
//            }
            if (!$form->getAuthor()) {
                $form->setError('author', 'Не указано имя');
            }
            if (!$form->getAuthorEmail()) {
                $form->setError('author_email', 'Не указан e-mail');
            }
//            if (!$form->getProductId()) {
//                $form->setError('product_id', 'Не задан id продукта');
//            }

            if ($form->isValid()) {
                $data = [
                    'pros' => $form->getPros(),
                    'cons' => $form->getCons(),
                    'extract' => $form->getExtract(),
                    'score' => $form->getScore(),
                    'author' => $form->getAuthor(),
                    'author_email' => $form->getAuthorEmail(),
                    'date' => '2013-11-14',//$form->getDate(),
//                    'product_id' => $form->getProductId(),
                ];

//                $params['http_user'] = 'admin';
//                $params['http_password'] = 'booToo9x';


                try {
                    $reviewsClient = \App::reviewsClient();
                    $result = [];
                    $reviewsClient->addQuery(
                        'add',
                        ['product_id' => $form->getProductId()],
                        $data,
                        function($data) use(&$result) {
                            //if ($data && is_array($data)) $result = reset($data);
                            $result = $data;
//                            $result = ['error' => ['code' => 0, 'message' => 'Одно из полей (pros, cons или extract) является обязательным']];
                        },
                        function(\Exception $e) {
                            \App::exception()->remove($e);
                        }
                    );
                    $reviewsClient->execute();
//                    print "<pre>";
//                    print_r($result['success']);
//                    print_r(\App::config()->shopScript['url'].'reviews/add');
//                    print "</pre>";
//                    exit;
//                    $result['success'] = true;

//                    if (!isset($result['success']) || !$result['success']) {
//                        \App::logger()->error(sprintf('Не удалось отправить отзыв у товара id=%s', $form->getProductId()));
//                        throw new \Exception('Отзыв не отправлен');
//                    }
                    return new \Http\JsonResponse($result);

//                    return new \Http\JsonResponse([
//                        'data'      => [],
//                        'success'   => true,
//                        'error'     => null,
//                        'notice'    => ['message' => 'Спасибо! Ваш отзыв появится на сайте после проверки модератором.', 'type' => 'info'],
//                    ]);
                } catch(\Exception $e) {
                    $form->setError('global', 'Неверно указаы данные формы' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
                }
            }

            $formErrors = [];
            foreach ($form->getErrors() as $fieldName => $errorMessage) {
                $formErrors[] = ['code' => 'invalid', 'message' => $errorMessage, 'field' => $fieldName];
            }

            // xhr
//            if ($request->isXmlHttpRequest()) {
                return new \Http\JsonResponse([
                    'form' => ['error' => $formErrors],
                    'error' => ['code' => 0, 'message' => 'Форма заполнена неверно'],
                ]);
//            }
        }

        return new \Http\JsonResponse(['success' => false]);
    }
}