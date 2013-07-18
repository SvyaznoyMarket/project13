<?php

namespace Controller\Survey;

class Action {
    public function submitAnswer(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        if (!$request->isMethod('post')) {
            throw new \Exception\NotFoundException('Request is not post http request');
        }

        $outCsvDelimiter = "\t";

        $question = $request->get('question');
        $answer = $request->get('answer');
        $kmId = $request->get('kmId');
        $userId = \App::user()->getEntity() ? \App::user()->getEntity()->getUserId() : '';

        // передав true в качестве параметра, получаем версию опроса,
        // кэшированную с момента открытия страницы - чтобы выходной файл соответствовал опросу
        $survey = \RepositoryManager::survey()->getEntity(true);
        \RepositoryManager::survey()->getEntity(true)->setIsAnswered(true);

        $outCsvFilePath = is_object($survey) ? $survey->getOutputFile() : \App::config()->surveyDir . '/survey.csv';
        if(!is_dir(\App::config()->surveyDir)) mkdir(\App::config()->surveyDir);
        if(!is_file($outCsvFilePath)) {
            touch($outCsvFilePath);
            file_put_contents($outCsvFilePath, 'Вопрос'.$outCsvDelimiter.'Ответ'.$outCsvDelimiter.'KM ID'.$outCsvDelimiter.'User ID'.$outCsvDelimiter."\n");
        }
        file_put_contents($outCsvFilePath, $question.$outCsvDelimiter.$answer.$outCsvDelimiter.$kmId.$outCsvDelimiter.$userId.$outCsvDelimiter."\n", FILE_APPEND);

        $result = [
            'success' => true,
        ];

        return new \Http\JsonResponse($result);
    }
}