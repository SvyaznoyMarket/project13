<?php

namespace Controller\Cron;

class IndexAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $content = '<h1>Задания планировщика</h1>' .
            '<div id="report_task_wrapper" class="mb5 fl">
                <h3>Отчеты для БЮ и SEO</h3>
                <div class="toLink cron_report_start">Сгенерировать</div>
                <div class="ml10 mt5 mb5" id="report_start_response"></div>
                <div class="toLink cron_report_links">Ссылки</div>
                <div class="ml10 mt5 mb5" id="report_links_response"></div>
            </div>' .
            '<div class="clear"></div>' .
            '<div class="mb25"></div>';

        $page = new \View\Cron\IndexPage();
        $page->setTitle('Задания планировщика');
        $page->setParam('content', $content);

        return new \Http\Response($page->show());
    }

}