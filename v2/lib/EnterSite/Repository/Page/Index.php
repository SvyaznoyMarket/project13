<?php

namespace EnterSite\Repository\Page;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\Index as Page;

class Index {
    use ConfigTrait, LoggerTrait, RouterTrait, ViewHelperTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, RouterTrait, ViewHelperTrait;
    }

    /**
     * @param Page $page
     * @param Index\Request $request
     */
    public function buildObjectByRequest(Page $page, Index\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $config = $this->getConfig();
        $router = $this->getRouter();
        $viewHelper = $this->getViewHelper();

        $templateDir = $config->mustacheRenderer->templateDir;

        $page->dataModule = 'index';

        $hosts = $config->mediaHosts;
        $host = reset($hosts);

        $promoData = [];
        foreach ($request->promos as $promoModel) {
            if (!$promoModel->image) {
                $this->getLogger()->push(['type' => 'warn', 'error' => sprintf('Нет картинки у промо #', $promoModel->id), 'action' => __METHOD__, 'tag' => ['promo']]);
                continue;
            }
            $promoItem = [
                'url'   => $router->getUrlByRoute(new Routing\Promo\Redirect($promoModel->id)),
                'image' => $host . $config->promo->urlPaths[1] . $promoModel->image,
            ];

            $promoData[] = $promoItem;
        }
        $page->content->promoDataValue = $viewHelper->json($promoData);

        // шаблоны mustache
        foreach ([

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

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}