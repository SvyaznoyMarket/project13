<?php

namespace Controller\Product\Reviews;

class Create {
    /**
     * @param \Http\Request $request
     * @param string $productUi
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productUi) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$productUi) {
            throw new \Exception('Не удалось получить ui продукта');
        }

        $responseData = [];
        $form = new \View\Product\ReviewForm();
        if ($request->isMethod('post')) {
            $form->fromArray((array)$request->get('review'));

            if (!$form->getAdvantage()) {
                $form->setError('advantage', 'Не указаны достоинства');
            }
            if (!$form->getDisadvantage()) {
                $form->setError('disadvantage', 'Не указаны недостатки');
            }
            if (!$form->getExtract()) {
                $form->setError('extract', 'Не указан комментарий');
            }
            if (!$form->getScore()) {
                $form->setError('score', 'Не указана оценка');
            }
            if (!$form->getAuthorName()) {
                $form->setError('author_name', 'Не указано имя');
            }
            if (!$form->getDate()) {
                $form->setError('date', 'Не указана дата');
            }
            if (!$form->getAuthorEmail()) {
                $form->setError('author_email', 'Не указан e-mail');
            } elseif (!strpos($form->getAuthorEmail(), '@')) {
                $form->setError('author_email', 'Указан не корректный e-mail');
            }

            // TODO SITE-2756 Полученную из формы оценку умножаем на 2
            $form->setScore($form->getScore() * 2);

            if ($form->isValid()) {
                try {
                    $data = [
                        'advantage'     => $form->getAdvantage(),
                        'disadvantage'  => $form->getDisadvantage(),
                        'extract'       => $form->getExtract(),
                        'score'         => $form->getScore(),
                        'author_name'   => $form->getAuthorName(),
                        'author_email'  => $form->getAuthorEmail(),
                        'date'          => $form->getDate(),
                    ];

                    \App::reviewsClient()->query('add', ['product_ui' => $productUi], $data, \App::config()->coreV2['hugeTimeout']);

                    return new \Http\JsonResponse([
                        'success'   => true,
                        'notice'    => ['message' => 'Спасибо! Ваш отзыв появится на сайте после проверки модератором.', 'type' => 'info'],
                    ]);
                } catch(\Exception $e) {
                    \App::exception()->remove($e);
                    \App::logger()->error('Не удалось обработать запрос' . (\App::config()->debug ? sprintf(': %s', $e->getMessage()) : ''));

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