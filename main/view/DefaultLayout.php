<?php

namespace View;

class DefaultLayout extends Layout {
    protected $layout  = 'layout-twoColumn';

    public function __construct() {
        parent::__construct();

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=900');
        $this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', 'Enter - новый способ покупать. Любой из ' . \App::config()->product['totalCount'] . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');

        $this->addStylesheet('/css/global.css');

        $this->addJavascript('/js/jquery-1.6.4.min.js');
        $this->addJavascript('/js/LAB.min.js');
        $this->addJavascript('/js/loadjs.js');
    }

    public function slotRelLink() {
        $request = \App::request();

        $tmp = explode('?', $request->getRequestUri());
        $tmp = reset($tmp);
        $path = str_replace(array('_filter', '_tag'), '', $tmp);
        if ('/' == $path) {
            $path = '';
        }


        $relLink = $request->getSchemeAndHttpHost() . $path;

        return '<link rel="canonical" href="' . $relLink . '" />';
    }

    public function slotGoogleAnalytics() {
        return $this->render('_googleAnalytics');
    }

    public function slotBodyDataAttribute() {
        return 'default';
    }

    public function slotBodyClassAttribute() {
        return '';
    }

    public function slotHeader() {
        return $this->render('_header', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->query('footer_default');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('_contentHead', $this->params);
    }

    public function slotContent() {
        return '';
    }

    public function slotSidebar() {
        return '';
    }

    public function slotRegionSelection() {
        /** @var $regions \Model\Region\Entity */
        $regions = $this->getParam('regionsToSelect', null);

        if (null === $regions) {
            try {
                $regions = \RepositoryManager::region()->getShowInMenuCollection();
            } catch (\Exception $e) {
                \App::logger()->error($e);

                $regions = [];
            }
        }

        return $this->render('_regionSelection', array_merge($this->params, array('regions' => $regions)));
    }

    public function slotInnerJavascript() {
        return ''
            . $this->render('_remarketingGoogle', ['tag_params' => []])
            . "\n\n"
            . $this->render('_innerJavascript');
    }

    public function slotAuth() {
        return $this->render('_auth');
    }

    public function slotUserbar() {
        return $this->render('_userbar');
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('_yandexMetrika') : '';
    }

    public function slotMyThings() {
        return (\App::config()->analytics['enabled'] && (bool)$this->getParam('myThingsData')) ? $this->render('_myThingsTracker', array('myThingsData' => $this->getParam('myThingsData'),)) : '';
    }

    public function slotMetaOg() {
        return '';
    }

    public function slotAdvanceSeoCounter() {
        return '';
    }

    public function slotAdriver() {
        return '';
    }

    public function slotMainMenu() {
        $repository = \RepositoryManager::menu();

        /** @var $menu \Model\Menu\Entity[] */
        $menu = [];
        $repository->prepareCollection(function($data) use (&$menu, $repository) {
            foreach ($data['item'] as $item) {
                $menu[] = new \Model\Menu\Entity($item);
            }
        });

        $categories = [];
        \RepositoryManager::productCategory()->prepareTreeCollection(\App::user()->getRegion(), 3, function($data) use (&$categories) {
            foreach ($data as $item) {
                $categories[] = new \Model\Product\Category\TreeEntity($item);
            }
        });

        \App::coreClientV2()->execute();

        $menu = $repository->getCollection(); //для тестирования

        $categoriesById = [];
        $walk = function($categories) use (&$walk, &$categoriesById, $repository) {
            foreach ($categories as $category) {
                /** @var \Model\Product\Category\Entity $category */
                $categoriesById[$category->getId()] = $category;

                if ((bool)$category->getChild()) {
                    $walk($category->getChild());
                }
            }
        };
        $walk($categories);
        unset($walk);

        $walk = function($menu) use (&$walk, &$categoriesById, $repository) {
            /** @var $iMenu \Model\Menu\Entity  */
            $i = 0;
            foreach ($menu as $iMenu) {
                $i++;
                $iMenu->setPriority($i);

                /** @var \Model\Menu\Entity $iMenu */
                $repository->setEntityLink($iMenu, \App::router(), $categoriesById);

                if (\Model\Menu\Entity::ACTION_PRODUCT_CATALOG == $iMenu->getAction()) {
                    $items = $iMenu->getItem();
                    $id = reset($items);
                    /** @var \Model\Product\Category\Entity $category */
                    $category = ($id && isset($categoriesById[$id])) ? $categoriesById[$id] : null;
                    if (!$category) {
                        \App::logger()->error(sprintf('Не найдена категория #%s для элемента меню %s', $id, $iMenu->getName()));
                        continue;
                    }

                    if (2 == $category->getLevel()) {
                        $iMenu->setImage($category->getImageUrl(0));
                    }

                    if ($category->getLevel() <= 2) {
                        $i = 1;
                        foreach ($category->getChild() as $childCategory) {
                            if ((2 == $category->getLevel()) && ($i > 5)) {
                                $child = new \Model\Menu\Entity();
                                $child->setAction(\Model\Menu\Entity::ACTION_PRODUCT_CATEGORY);
                                $child->setName('Все разделы');
                                $child->setItem([$category->getId()]);
                                $iMenu->addChild($child);

                                break;
                            }

                            $child = new \Model\Menu\Entity();
                            $child->setAction(\Model\Menu\Entity::ACTION_PRODUCT_CATALOG);
                            $child->setName($childCategory->getName());
                            $child->setItem([$childCategory->getId()]);
                            $iMenu->addChild($child);

                            $i++;
                        }
                    }
                }

                if ((bool)$iMenu->getChild()) {
                    $walk($iMenu->getChild());
                }
            }
        };
        $walk($menu);
        unset($walk);

        return $this->render('_mainMenu', array('menu' => $menu));
    }

    public function slotBanner() {
        return '';
    }
}
