<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $config array
 */
?>

<?
$appConfig = \App::config();
$router = \App::router();

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
    'jsonLog'               => $appConfig->jsonLog['enabled'],
    'userUrl'               => $router->generate('user.info'),
    'routeUrl'              => $router->generate('route'),
    'f1Certificate'         => $appConfig->f1Certificate['enabled'],
    'coupon'                => $appConfig->coupon['enabled'],
    'addressAutocomplete'   => $appConfig->order['addressAutocomplete'],
    'prepayment'            => $appConfig->order['prepayment'],
    'isMobile' => $isMobile,
    'user' => [
        'region' => [
            'forceDefaultBuy' => \App::user()->getRegion()->getForceDefaultBuy(),
        ],
    ],
    'routes' => [
        'cart'             => ['pattern' => $routerRules['cart']['pattern']],
        'cart.product.set' => ['pattern' => $routerRules['cart.product.set']['pattern']],
        'cart.oneClick.product.set' => ['pattern' => $routerRules['cart.oneClick.product.set']['pattern']],
        'compare.add'      => ['pattern' => $routerRules['compare.add']['pattern']],
        'compare.delete'   => ['pattern' => $routerRules['compare.delete']['pattern']],
    ],
], isset($config) ? (array)$config : []);
?>

<div id="page-config" data-value="<?= $page->json($config) ?>"></div>
