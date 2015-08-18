<?php
/**
 * @var $page    \View\Layout
 * @var $user    \Session\User
 */

// стр. корзины

$productIds = [];
$productQuantities = [];
foreach ($user->getCart()->getProductsById() as $cartProduct) {
    $productIds[] = $cartProduct->id;
    $productQuantities[] = $cartProduct->quantity;
}

$data = [
    'page'              => 'cart',
    'productIds'        => implode(',', $productIds),       // где XX,YY,ZZ – это ID товаров в корзине через запятую.
    'productQuantities' => implode(',', $productQuantities),// где X,Y,Z – это количество соответствующих товаров (опционально).
];
?>
<div id="xcntmyAsync" class="jsanalytics" data-value="<?= $page->json($data) ?>"></div>
