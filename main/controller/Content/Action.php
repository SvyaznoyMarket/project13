<?php

namespace Controller\Content;

class Action {

    public function execute(\Http\Request $request, $token) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::contentClient();

        $content = $client->query($token, [], false);

        if (!(bool)$content) {
            throw new \Exception\NotFoundException();
        }

        $page = new \View\Content\IndexPage();
        $page->setTitle($content['title']);
        $page->setParam('content', $content['content']);
        //нужно для увеличения отступа от заголовкой и строки поика
        $page->setParam('extendedMargin', true);
        if (!(bool)$content['layout'])
        {
            $page->setParam('title', $content['title']);
            //нужно, чтобы после заголовка и строки поиска была линия
            $page->setParam('hasSeparateLine', true);
        }
        else {
            $page->setParam('breadcrumbs', null);
        }

        return new \Http\Response($page->show());
    }
}