<?php

namespace Controller\Cron;

class Action {

    public function execute(\Http\Request $request, $task) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $consoleFilepath = \App::config()->appDir . '/console.php';

        $dateStart = new \DateTime();

        switch ($task) {
            case 'report':
                $sourceCsvDir = \App::config()->cmsDir . '/v1/logs/accessory';
                $reportDir = $sourceCsvDir . '/report';
                if(!is_dir($sourceCsvDir)) mkdir($sourceCsvDir);
                if(!is_dir($reportDir)) mkdir($reportDir);

                if(!is_file($reportDir . '/' . $dateStart->format('YmdH') . '.lock')) {
                    system("php ". $consoleFilepath . " Command/BuSeoReportAction generate " . \App::$env . " > /dev/null &");
                    $content = '<h2>Задание планировщика "'.$task.'" запущено</h2>';
                } else {
                    $content = '<h2>Задание планировщика "'.$task.'" уже было запущено ранее</h2>';
                }

                $content .= '<div class="mb15">После завершения генерации отчеты будут доступны на <a href="https://github.com/SvyaznoyMarket/cms/tree/sandbox/v1/logs/accessory/report">GitHub</a></div>';
                break;
            
            default:
                throw new \Exception\NotFoundException();
                break;
        }

        $page = new \View\Cron\IndexPage();
        $page->setTitle('Задание планировщика "'.$task.'" запущено');
        $page->setParam('content', $content);

        return new \Http\Response($page->show());
    }

}