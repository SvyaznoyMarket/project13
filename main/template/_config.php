<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $config array
 */
?>

<?
$appConfig = \App::config();
$router = \App::router();
$helper = new \Helper\TemplateHelper();

try {
    $classFile = $appConfig->appDir . '/vendor/Mobile-Detect/Mobile_Detect.php';
    if (file_exists($classFile)) {
        include_once $classFile;
        $mobileDetect = new \Mobile_Detect();
        $isMobile = $mobileDetect->isMobile();
    } else {
        \App::logger()->error('Класс Mobile_Detect не найден', ['mobile']);
        $isMobile = false;
    }
} catch (\Exception $e) {
    $isMobile = false;
}

$analytics = [];

if (\App::config()->partners['soloway']['enabled']) {
    $analytics['soloway'] = [
        'id' => \App::config()->partners['soloway']['id'],
    ];
}

if (\App::config()->flocktory['postcheckout']) {
    $analytics['flocktory']['postcheckout'] = [
        'enabled' => true,
    ];
}

$routerRules = \App::router()->getRules();
$config = array_merge([
    'useNodeMQ'             => $appConfig->useNodeMQ,
    'nodeMQConfig'          => $appConfig->nodeMQ,
    'adfoxEnabled'          => $appConfig->adFox['enabled'],
    'jsonLog'               => $appConfig->jsonLog['enabled'],
    'routeUrl'              => $router->generateUrl('route'),
    'addressAutocomplete'   => $appConfig->order['addressAutocomplete'],
    'prepayment'            => $appConfig->order['prepayment'],
    'isMobile'              => $isMobile,
    'currentRoute'          => \App::request()->routeName,
    'location'              => [],
    'user' => [
        'region' => [
            'forceDefaultBuy' => \App::user()->getRegion()->getForceDefaultBuy(),
            'kladrId'         => \App::user()->getRegion()->kladrId,
            'name'            => \App::user()->getRegion() ? \App::user()->getRegion()->getName() : null
        ],
    ],
    'request' => [
        'route' => [
            'name' => \App::request()->routeName,
            'pathVars' => \App::request()->routePathVars->all(),
        ],
    ],
    'globalParams' => $router->getGlobalParams(),
    'routes' => call_user_func(function() use($routerRules, $appConfig) {
        $routes = [
            \App::request()->routeName => [],
            'region.change' => [],
            'cart' => [],
            'cart.product.setList' => [],
            'compare.add' => [],
            'compare.delete' => [],
            'orderV3OneClick.delivery' => [],
            'product.category' => [],
            'product.kit' => [],
            'orderV3.delivery' => [],
            'orderV3OneClick.form' => [],
            'order.slot.create' => [],
            'product.reviews.get' => [],
            'ajax.product.delivery.map' => [],
        ];

        foreach ($routes as $routeName => $route) {
            $routes[$routeName] = [
                'urls' => array_map(function($url) use($appConfig) {
                    return $appConfig->routeUrlPrefix . $url;
                }, $routerRules[$routeName]['urls']),
                'require' => isset($routerRules[$routeName]['require']) ? $routerRules[$routeName]['require'] : [],
                'outFilters' => isset($routerRules[$routeName]['outFilters']) ? $routerRules[$routeName]['outFilters'] : [],
            ];
        }

        return $routes;
    }),
    'selfDeliveryTest'    => \Session\AbTest\AbTest::isSelfPaidDelivery(), // удалять осторожно, поломается JS
    'selfDeliveryLimit'    => $appConfig->self_delivery['limit'], // стоимость платного самовывоза, удалять осторожно, поломается JS
    'minOrderSum'  => \App::abTest()->isOrderMinSumRestriction() ? $appConfig->minOrderSum : false,
    'infinityScroll' => \App::abTest()->isInfinityScroll(),
    'analytics' => $analytics,
    'meta' => [
        'category' => [
            'views' => [
                'expanded' => [
                    'id' => \Model\Product\Category\Entity::VIEW_EXPANDED,
                ],
            ],
        ],
    ],
], isset($config) ? (array)$config : []);
?>

<div id="page-config" data-value="<?= $page->json($config) ?>"></div>