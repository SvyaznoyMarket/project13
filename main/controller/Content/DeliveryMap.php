<?php

namespace Controller\Content;


use View\Content\DeliveryMapPage;

class DeliveryMap {

    public function execute() {

        $page = new DeliveryMapPage();

        // Используем страницу контактов чтобы получить меню
        $client = \App::contentClient();
        $client->addQuery('contacts', [], function($data) use (&$sidebar) {
            $content = (string)@$data['content'];
            $content = str_replace(array("\r", "\n"), '', $content);
            preg_match('/\<!-- Menu start --\>(.*)\<!-- Menu end --\>/mu', $content, $matches);
            $sidebar = @$matches[1];
        });
        $client->execute();

        $page->setParam('sidebar', $sidebar);
        $page->setParam('title', 'Магазины и точки самовывоза');
        return new \Http\Response($page->show());
    }

}