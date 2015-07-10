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

$routerRules = \App::router()->getRules();
$config = array_merge([
    'adfoxEnabled'     => $appConfig->adFox['enabled'],
    'jsonLog'               => $appConfig->jsonLog['enabled'],
    'userUrl'               => $router->generate('user.info'),
    'routeUrl'              => $router->generate('route'),
    'f1Certificate'         => $appConfig->f1Certificate['enabled'],
    'addressAutocomplete'   => $appConfig->order['addressAutocomplete'],
    'prepayment'            => $appConfig->order['prepayment'],
    'isMobile'              => $isMobile,
    'currentRoute'          => \App::request()->attributes->get('route'),
    'location'              => [],
    'user' => [
        'region' => [
            'forceDefaultBuy' => \App::user()->getRegion()->getForceDefaultBuy(),
            'kladrId'         => \App::user()->getRegion()->kladrId,
            'name'            => \App::user()->getRegion() ? \App::user()->getRegion()->getName() : null
        ],
    ],
    'routes' => [
        'cart'                      => ['pattern' => $routerRules['cart']['pattern']],
        'cart.product.set'          => ['pattern' => $routerRules['cart.product.set']['pattern']],
        'cart.oneClick.product.set' => ['pattern' => $routerRules['cart.oneClick.product.set']['pattern']],
        'compare.add'               => ['pattern' => $routerRules['compare.add']['pattern']],
        'compare.delete'            => ['pattern' => $routerRules['compare.delete']['pattern']],
        'orderV3OneClick.delivery'  => ['pattern' => $routerRules['orderV3OneClick.delivery']['pattern']],
        'product.category'          => ['pattern' => $routerRules['product.category']['pattern']],
        'product.kit'               => ['pattern' => $routerRules['product.kit']['pattern']],
        'orderV3OneClick.form'      => ['pattern' => $routerRules['orderV3OneClick.form']['pattern']],
        'order.slot.create'         => ['pattern' => $routerRules['order.slot.create']['pattern']],
        'product.reviews.get'       => ['pattern' => $routerRules['product.reviews']['pattern']],
    ],
    'newProductPage' => \App::abTest()->isNewProductPage(),
    'selfDeliveryTest'    => \Session\AbTest\AbTest::isSelfPaidDelivery(), // удалять осторожно, поломается JS
    'selfDeliveryLimit'    => $appConfig->self_delivery['limit'], // стоимость платного самовывоза, удалять осторожно, поломается JS
    'minOrderSum'  => \App::abTest()->isOrderMinSumRestriction() ? $appConfig->minOrderSum : false,
], isset($config) ? (array)$config : []);
?>

<div id="page-config" data-value="<?= $page->json($config) ?>"></div>