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
        if ($user->getToken()) {
            \RepositoryManager::getUser()->prepareEntityByToken($user->getToken(), function($data) {
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
            \RepositoryManager::getRegion()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = array();
        \RepositoryManager::getRegion()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // услуги
        /** @var $services \Model\Product\Service\Entity[] */
        $categories = array();
        \RepositoryManager::getServiceCategory()->prepareRootCollection($region, function($data) use (&$categories) {
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
        $page->setParam('rootCategories', $rootCategories);
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
            \RepositoryManager::getUser()->prepareEntityByToken($user->getToken(), function($data) {
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
            \RepositoryManager::getRegion()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = array();
        \RepositoryManager::getRegion()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // запрашиваем категорию услуги
        /** @var $category \Model\Product\Service\Category\Entity */
        $category = null;
        \RepositoryManager::getServiceCategory()->prepareEntityByToken($categoryToken, $region, function($data) use(&$category) {
            if ((bool)$data) {
                $category = new \Model\Product\Service\Category\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (2 == $category->getLevel()) {
            $children = $category->getChild();
            /** @var $category \Model\Product\Service\Category\Entity */
            $category = reset($children);
        }

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория услуги с токеном "%s" не найдена', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        // ид услуг
        $serviceIds = array();
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
            \RepositoryManager::getService()->prepareIdsByCategory($child, $region, $callback);
        }

        // все категории услуг
        /** @var $allCategories \Model\Product\Service\Category\Entity[] */
        $allCategories = array();
        \RepositoryManager::getServiceCategory()->prepareCollection($region, function($data) use (&$allCategories) {
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
        $servicesByCategory = array();
        if ((bool)$serviceIds) {
            \RepositoryManager::getService()->prepareCollectionById($serviceIds, $region, function($data) use(&$servicesByCategory) {
                foreach ($data as $item) {
                    $service = new \Model\Product\Service\Entity($item);
                    /** @var $serviceCategory \Model\Product\Service\Category\Entity */
                    $categories = $service->getCategory();
                    $serviceCategory = array_pop($categories);
                    if (!$serviceCategory) continue;
                    if (!isset($servicesByCategory[$serviceCategory->getId()])) {
                        $servicesByCategory[$serviceCategory->getId()] = array();
                    }
                    $servicesByCategory[$serviceCategory->getId()][] = $service;
                }
            });

            // выполнение 4-го пакета запросов
            $client->execute();
        }

        $page = new \View\Service\CategoryPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('rootCategories', $rootCategories);
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
            \RepositoryManager::getUser()->prepareEntityByToken($user->getToken(), function($data) {
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
            \RepositoryManager::getRegion()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = array();
        \RepositoryManager::getRegion()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // услуга
        /** @var $service \Model\Product\Service\Entity */
        $service = null;
        \RepositoryManager::getService()->prepareEntityByToken($serviceToken, $region, function($data) use (&$service) {
            $data = reset($data);
            if ((bool)$data) {
                $service = new \Model\Product\Service\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$service) {
            throw new \Exception\NotFoundException(sprintf('Service with token %s not found', $serviceToken));
        }

        if ((bool)$service->getAlikeId()) {
            // подготовка 3-го пакета запросов

            // похожие услуги
            \RepositoryManager::getService()->prepareCollectionById($service->getAlikeId(), $region, function($data) use (&$service) {
                foreach ($data as $item) {
                    $service->addAlike(new \Model\Product\Service\Entity($item));
                }
            });

            // выполнение 3-го пакета запросов
            $client->execute();
        }

        $page = new \View\Service\ShowPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('service', $service);

        return new \Http\Response($page->show());
    }
}