<?php
/**
 * @var \Model\Product\Entity $product
 * @var \Helper\TemplateHelper $helper
 */

if (!isset($helper)) $helper = new \Helper\TemplateHelper();

$id = $product->getId();
$typeId = $product->getType() ? $product->getType()->getId() : null;
?>

<?= $helper->renderWithMustache('compare/_button-product-compare', [
    'id'                => $id,
    'typeId'            => $typeId,
    'addUrl'            => \App::router()->generate('compare.add', ['productId' => $id, 'location' => 'product']),
    'url'               => \App::router()->generate('compare', ['typeId' => $typeId]),
    'isSlot'            => (bool)$product->getSlotPartnerOffer(),
    'isOnlyFromPartner' => $product->isOnlyFromPartner(),
]) ?>

