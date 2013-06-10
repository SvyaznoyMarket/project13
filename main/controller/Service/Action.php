<?php

namespace Controller\Service;

class Action {
    /**
     * @return \Http\Response
     */
    public function index() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        /*if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }*/

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // услуги
        /** @var $services \Model\Product\Service\Entity[] */
        $categories = [];
        \RepositoryManager::serviceCategory()->prepareRootCollection($region, function($data) use (&$categories) {
            if (!isset($data['children']) || !is_array($data['children'])) {
                $e = new \Exception('Неверные данные для категорий услуг');
                \App::exception()->add($e);
                \App::logger()->error($e);
                return;
            }
            foreach ($data['children'] as $item) {
                $categories[] = new \Model\Product\Service\Category\Entity($item);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        $page = new \View\Service\IndexPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('categories', $categories);

        return new \Http\Response($page->show());
    }

    public function category($categoryToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем категорию услуги
        /** @var $category \Model\Product\Service\Category\Entity */
        $category = null;
        \RepositoryManager::serviceCategory()->prepareEntityByToken($categoryToken, $region, function($data) use(&$category) {
            if ((bool)$data) {
                $category = new \Model\Product\Service\Category\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if ($category && (2 == $category->getLevel())) {
            $children = $category->getChild();
            /** @var $category \Model\Product\Service\Category\Entity */
            $category = reset($children);
        }

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория услуги @%s не найдена', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        // ид услуг
        $serviceIds = [];
        $callback = function($data) use (&$serviceIds, $category) {
            if (!isset($data['list']) || !is_array($data['list'])) {
                $e = new \Exception(sprintf('Не получены услуги для категории %s', $category->getId()));
                \App::exception()->add($e);
                \App::logger()->error($e);

                return;
            }
            $serviceIds = array_merge($serviceIds, $data['list']);
        };
        foreach ($category->getChild() as $child) {
            \RepositoryManager::service()->prepareIdsByCategory($child, $region, $callback);
        }

        // все категории услуг
        /** @var $allCategories \Model\Product\Service\Category\Entity[] */
        $allCategories = [];
        \RepositoryManager::serviceCategory()->prepareCollection($region, function($data) use (&$allCategories) {
            if (!isset($data['children']) || !is_array($data['children'])) {
                $e = new \Exception('Неверные данные для категорий услуг');
                \App::exception()->add($e);
                \App::logger()->error($e);
                return;
            }
            foreach ($data['children'] as $item) {
                $allCategories[] = new \Model\Product\Service\Category\Entity($item);
            }
        });

        // выполнение 3-го пакета запросов
        $client->execute();

        // подготовка 4-го пакета запросов

        // услуги
        /** @var $servicesByCategory \Model\Product\Service\Entity[] */
        $servicesByCategory = [];
        if ((bool)$serviceIds) {
            \RepositoryManager::service()->prepareCollectionById($serviceIds, $region, function($data) use(&$servicesByCategory) {
                foreach ($data as $item) {
                    $service = new \Model\Product\Service\Entity($item);
                    /** @var $serviceCategory \Model\Product\Service\Category\Entity */
                    $categories = $service->getCategory();
                    $serviceCategory = array_pop($categories);
                    if (!$serviceCategory) continue;
                    if (!isset($servicesByCategory[$serviceCategory->getId()])) {
                        $servicesByCategory[$serviceCategory->getId()] = [];
                    }
                    $servicesByCategory[$serviceCategory->getId()][] = $service;
                }
            });

            // выполнение 4-го пакета запросов
            $client->execute();
        }

        $page = new \View\Service\CategoryPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('category', $category);
        $page->setParam('servicesByCategory', $servicesByCategory);
        $page->setParam('allCategories', $allCategories);

        return new \Http\Response($page->show());
    }

    public function show($serviceToken) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // услуга
        /** @var $service \Model\Product\Service\Entity */
        $service = null;
        \RepositoryManager::service()->prepareEntityByToken($serviceToken, $region, function($data) use (&$service) {
            $data = reset($data);
            if ((bool)$data) {
                $service = new \Model\Product\Service\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$service) {
            throw new \Exception\NotFoundException(sprintf('Service @%s not found', $serviceToken));
        }

        if ((bool)$service->getAlikeId()) {
            // подготовка 3-го пакета запросов

            // похожие услуги
            \RepositoryManager::service()->prepareCollectionById($service->getAlikeId(), $region, function($data) use (&$service) {
                foreach ($data as $item) {
                    $service->addAlike(new \Model\Product\Service\Entity($item));
                }
            });

            // выполнение 3-го пакета запросов
            $client->execute();
        }

        $page = new \View\Service\ShowPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('service', $service);

        return new \Http\Response($page->show());
    }
}