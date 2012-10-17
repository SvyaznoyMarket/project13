<?php
/**
 * @var $page     \View\Layout
 * @var $user     \Session\User
 * @var $product  \Model\Product\Entity
 * @var $view     string
 * @var $quantity int
 */
?>

<?php
if (empty($view)) {
    $view = 'default';
}

if (empty($quantity)) {
    $quantity = 1;
}

$disabled = !$product->getIsBuyable();

switch ($view) {
    case 'default':
        require __DIR__ . '/button/default.php';
        break;
    case 'large':
        require __DIR__ . '/button/large.php';
        break;
}
?>