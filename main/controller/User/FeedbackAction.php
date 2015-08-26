<?php

namespace Controller\User;


use Exception\AccessDeniedException;
use Http\File\UploadedFile;
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
        $files = $request->files->all();
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
            $mailer = new \PHPMailer();
            $mailer->addAddress($this->email);
            $mailer->From = $query[$fieldEmail];
            $mailer->Subject = $query[$fieldSubject];
            $mailer->Body = $query[$fieldMessage];
            foreach ($files as $file) {
                if (empty($file)) {
                    continue;
                }

                /** @var $file UploadedFile */
                $mailer->addAttachment($file->getPathname(), $file->getClientOriginalName());
            }
            if (!$mailer->send()) {
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