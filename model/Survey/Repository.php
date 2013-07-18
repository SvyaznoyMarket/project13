<?php

namespace Model\Survey;

class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\DataStore\Client $client) {
        $this->client = $client;
    }


    /**
     * @param  bool $cached
     * @return Entity|null
     *
     * передав true в качестве параметра, получаем версию опроса,
     * кэшированную с момента открытия страницы - чтобы выходной файл при ответе на вопрос
     * соответствовал опросу
     */
    public function getEntity($cached = false) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $entity = \App::session()->get('survey');

        if (!$entity || !$cached) {
            $client = clone $this->client;
            $data = $this->client->query('survey/survey.json');
            $newEntity = (bool)$data ? new Entity($data) : null;

            // сбрасываем счетчик времени
            if($newEntity && (!$entity || $entity && (
                $entity->getQuestion() != $newEntity->getQuestion() ||
                $entity->getIsActive() != $newEntity->getIsActive()))) {
                $newEntity->setInitTime(new \DateTime());
            } 

            // если вопрос не изменился, сохраняем информацию о том отвечал ли на него пользователь
            // и о времени инициализации счетчика времени
            if($entity && $newEntity && $entity->getQuestion() == $newEntity->getQuestion()) {
                $newEntity->setIsAnswered($entity->getIsAnswered());
                $newEntity->setInitTime($entity->getInitTime());
            }

            $entity = $newEntity;

            \App::session()->set('survey', $entity);
        }

        return $entity;
    }

}