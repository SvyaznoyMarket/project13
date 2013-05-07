<?php

namespace Controller\Cron;

class Action {

    public function execute(\Http\Request $request, $task) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $taskFilepath = \App::config()->appDir . '/cron/'. $task. '.php';

        if(!is_file($taskFilepath))
            throw new \Exception\NotFoundException();

        $dateStart = new \DateTime();
        $reportDir = \App::config()->appDir . '/report';
        $sourceCsvDir = \App::config()->appDir . '/report/source';

        if(!is_file($reportDir . '/' . $dateStart->format('YmdH') . '.lock')) {
            system("php ".$taskFilepath." > /dev/null &");
            $content = '<h2>Задание планировщика "'.$task.'" запущено</h2>';
        } else {
            $content = '<h2>Задание планировщика "'.$task.'" уже было запущено ранее</h2>';
        }

        $content .= '<div class="mb15">После завершения генерации отчеты будут доступны по ссылкам:</div><ul class="mb25">';

        foreach (scandir($sourceCsvDir) as $file) {
            if(preg_match('/^(.+)\.csv$/', $file, $matches)) {
                $rootCategory = $matches[1];
                $content .= "<li><a href='http://www.enter.ru/report/" . $dateStart->format('YmdH') . '_' . $rootCategory . "_accessories_bu.csv'>Скачать отчет для БЮ (" . $rootCategory . ")</a></li>".
                "<li><a href='http://www.enter.ru/report/" . $dateStart->format('YmdH') . '_' . $rootCategory . "_accessories_seo.csv'>Скачать отчет для SEO (" . $rootCategory . ")</a></li>";
            }
        }
        $content .= '</ul>';

        $page = new \View\Cron\IndexPage();
        $page->setTitle('Задание планировщика "'.$task.'" запущено');
        $page->setParam('content', $content);

        return new \Http\Response($page->show());
    }
}