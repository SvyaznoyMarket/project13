<?php

namespace Controller\User;

class GetAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        try {
            $token = trim((string)$request->get('token'));
            if (!$token) {
                throw new \Exception('Не передан token');
            }

            $user = \RepositoryManager::user()->getEntityByToken($token);
            if (!$user) {
                throw new \Exception('Пользователь не найден', 404);
            }

            $responseData = [
                'success' => true,
                'user'    => [
                    'birthday'    => $user->getBirthday() instanceof \DateTime ? $user->getBirthday()->format('Y-m-d') : null,
                    'email'       => $user->getEmail(),
                    'firstName'   => $user->getFirstName(),
                    'lastName'    => $user->getLastName(),
                    'mobilePhone' => $user->getMobilePhone(),
                    'sex'         => $user->getSex(),
                ],
            ];
        } catch (\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                ],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}