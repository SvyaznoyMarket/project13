<?php

namespace EnterSite\Repository\Page;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Page\DefaultLayout as Page;

class DefaultLayout {
    use ConfigTrait, RouterTrait, LoggerTrait, ViewHelperTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Page $page
     * @param DefaultLayout\Request $request
     */
    public function buildObjectByRequest(Page $page, DefaultLayout\Request $request) {
        $config = $this->getConfig();
        $helper = $this->getViewHelper();
        $router = $this->getRouter();

        $templateDir = $config->mustacheRenderer->templateDir;

        // стили
        $page->styles[] = '/v2/css/global.css';

        // заголовок
        $page->title = 'Enter - все товары для жизни по интернет ценам!';

        // body[data-version]
        $page->dataVersion = date('ymd');

        // body[data-module]
        $page->dataModule = 'default';

        // body[data-value]
        $page->dataConfig = $helper->json([
            'requestId' => $config->requestId,
            'user'      => [
                'infoUrl'    => $router->getUrlByRoute(new Routing\User\Get()),
            ],
        ]);

        // регион
        $page->regionBlock->regionName = $request->region->name;
        $page->regionBlock->autocompleteUrl = $router->getUrlByRoute(new Routing\Region\Autocomplete());
        foreach ([ // TODO: вынести в конфиг
            ['id' => '14974', 'name' => 'Москва'],
            ['id' => '108136', 'name' => 'Санкт-Петербург'],
        ] as $regionItem) {
            $region = new Page\RegionBlock\Region();
            $region->name = $regionItem['name'];
            $region->url = $router->getUrlByRoute(new Routing\Region\Set($regionItem['id']));

            $page->regionBlock->regions[] = $region;
        }

        // главное меню
        $page->mainMenu = $request->mainMenu;

        // пользователь
        $page->userBlock->isUserAuthorized = false;
        $page->userBlock->userLink->url = $router->getUrlByRoute(new Routing\User\Auth());
        $page->userBlock->cart->url = $router->getUrlByRoute(new Routing\Cart\Index());

        // шаблоны mustache
        foreach ([
            [
                'id'   => 'tpl-product-buyButton',
                'name' => 'partial/cart/button',
            ],
            [
                'id'   => 'tpl-product-buySpinner',
                'name' => 'partial/cart/spinner',
            ],
            [
                'id'   => 'tpl-user',
                'name' => 'partial/user',
            ],
        ] as $templateItem) {
            try {
                $template = new Model\Page\DefaultLayout\Template();
                $template->id = $templateItem['id'];
                $template->content = file_get_contents($templateDir . '/' . $templateItem['name'] . '.mustache');

                $page->templates[] = $template;
            } catch (\Exception $e) {
                $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['template']]);
            }
        }
    }
}