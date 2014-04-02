<?php

namespace EnterSite\Repository\Page;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Page\DefaultLayout as Page;

class DefaultLayout {
    use ConfigTrait;
    use RouterTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Page $page
     * @param DefaultLayout\Request $request
     */
    public function buildObjectByRequest(Page $page, DefaultLayout\Request $request) {
        $config = $this->getConfig();
        $templateDir = $config->mustacheRenderer->templateDir;

        // стили
        $page->styles[] = '/v2/css/global.css';

        // заголовок
        $page->title = 'Enter - все товары для жизни по интернет ценам!';

        // главное меню
        $page->mainMenu = $request->mainMenu;

        // шаблоны mustache
        try {
            $page->templateBlock->cartBuyButton = file_get_contents($templateDir . '/partial/cart/button.mustache');
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['template']]);
        }
    }
}