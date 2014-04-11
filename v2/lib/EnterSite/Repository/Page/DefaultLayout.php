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
    use ConfigTrait;
    use RouterTrait, LoggerTrait, ViewHelperTrait {
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

        // body[data-value]
        $page->bodyDataConfig = $helper->json([
            'requestId'      => $config->requestId,
            'user' => [
                'infoCookie' => $config->userToken->infoCookieName,
                'infoUrl'    => $router->getUrlByRoute(new Routing\User\Get()),
            ],
        ]);

        // главное меню
        $page->mainMenu = $request->mainMenu;

        // шаблоны mustache
        foreach ([
            ['id' => 'tpl-product-buyButton', 'file' => '/partial/cart/button.mustache'],
            ['id' => 'tpl-product-buySpinner', 'file' => '/partial/cart/spinner.mustache'],
        ] as $templateItem) {
            try {
                $template = new Model\Page\DefaultLayout\Template();
                $template->id = $templateItem['id'];
                $template->content = file_get_contents($templateDir . $templateItem['file']);

                $page->templates[] = $template;
            } catch (\Exception $e) {
                $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['template']]);
            }
        }
    }
}