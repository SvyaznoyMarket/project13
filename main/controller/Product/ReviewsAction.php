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

        if (!$productId) {
            throw new \Exception('Не удалось получить id продукта');
        }

        $responseData = [];
        $form = new \View\Product\ReviewForm();
        if ($request->isMethod('post')) {
            $form->fromArray((array)$request->get('review'));
            $form->setScore(10);

            if (!$form->getPros()) {
                $form->setError('pros', 'Не указаны достоинства');
            }
            if (!$form->getCons()) {
                $form->setError('cons', 'Не указаны недостатки');
            }
            if (!$form->getExtract()) {
                $form->setError('extract', 'Не указан комментарий');
            }
            if (!$form->getScore()) {
                $form->setError('score', 'Не указана оценка');
            }
            if (!$form->getAuthor()) {
                $form->setError('author', 'Не указано имя');
            }
            if (!$form->getAuthorEmail()) {
                $form->setError('author_email', 'Не указан e-mail');
            } elseif (!strpos($form->getAuthorEmail(), '@')) {
                $form->setError('author_email', 'Указан не корректный e-mail');
            }

            if ($form->isValid()) {
                try {
                    $reviewsClient = \App::reviewsClient();
                    $result = [];
                    $exception = null;
                    $reviewsClient->addQuery(
                        'add',
                        [
                            'product_id' => $productId
                        ],
                        [
                            'pros' => $form->getPros(),
                            'cons' => $form->getCons(),
                            'extract' => $form->getExtract(),
                            'score' => $form->getScore(),
                            'author' => $form->getAuthor(),
                            'author_email' => $form->getAuthorEmail(),
                            'date' => date('Y-m-d'),
                        ],
                        function($data) use(&$result) {
                            $result = $data;
                        },
                        function(\Exception $e) use (&$exception) {
                            \App::exception()->remove($e);
                            $exception = $e;
                        }
                    );
                    $reviewsClient->execute();

                    if ($exception instanceof \Exception) {
                        throw new \Exception('Не удалось обработать запрос' . (\App::config()->debug ? sprintf(': %s', $exception->getMessage()) : ''), $exception->getCode());
                    }

                    return new \Http\JsonResponse([
                        'success'   => true,
                        'notice'    => ['message' => 'Спасибо! Ваш отзыв появится на сайте после проверки модератором.', 'type' => 'info'],
                    ]);
                } catch(\Exception $e) {
                    $form->setError('global', 'Отзыв не отправлен' . (\App::config()->debug ? (': ' . $e->getMessage()) : ''));
                }
            }

            $formErrors = [];
            foreach ($form->getErrors() as $fieldName => $errorMessage) {
                $formErrors[] = ['code' => 'invalid', 'message' => $errorMessage, 'field' => $fieldName];
            }

            $responseData = [
                'form' => ['error' => $formErrors],
                'error' => ['code' => 0, 'message' => 'Форма заполнена неверно'],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}