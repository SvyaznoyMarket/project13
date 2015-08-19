<?php

namespace Controller\User;


use Exception\AccessDeniedException;
use Http\Request;
use Http\JsonResponse;

class FeedbackAction
{

    protected $email;

    /**
     * @throws \Exception
     * @throws AccessDeniedException
     */
    public function __construct() {

        $this->email = \App::config()->feedback['email'];

        if (!\App::config()->feedback['enabled']) {
            throw new AccessDeniedException('Форма обратной связи выключена');
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('Некорректный email в конфигурации');
        }
    }

    /** Отправка сообщения на email
     * TODO отправка вложенного файла
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function execute(Request $request) {

        // Шаблон сообщения
        $response = [
            'success'   => false,
            'errors'    => []
        ];

        $errors = [];

        $query = $request->request->all();
        $fieldEmail = 'email';
        $fieldSubject = 'subject';
        $fieldMessage = 'message';

        if (!filter_var($query[$fieldEmail], FILTER_VALIDATE_EMAIL)) {
            $errors[] = [
                'field' => $fieldEmail,
                'message' => 'Ошибка валидации email адреса'
            ];
        }

        if (empty($query[$fieldSubject])) {
            $errors[] = [
                'field' => $fieldSubject,
                'message' => 'Поле "Тема" не может быть пустым'
            ];
        }

        if (empty($query[$fieldMessage])) {
            $errors[] = [
                'field' => $fieldMessage,
                'message' => 'Поле "Сообщение" не может быть пустым'
            ];
        }

        if (!$errors) {
            if (!mail($this->email, $query[$fieldSubject], $query[$fieldMessage])) {
                $errors[] = [
                    'field' => null,
                    'message'   => 'Ошибка при отправке сообщения'
                ];
            }
        }

        $response['errors'] = $errors;
        $response['success'] = !$errors;

        return new JsonResponse($response);

    }

}