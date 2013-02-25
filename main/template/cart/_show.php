<?php
/**
 * @var $page             \View\Layout
 * @var $cart             \Session\Cart
 * @var $creditEnabled    bool
 * @var $products         \Model\Product\CartEntity[]
 * @var $services         \Model\Product\Service\Entity[]
 * @var $cartProductsById \Model\Cart\Product\Entity[]
 * @var $cartServicesById \Model\Cart\Service\Entity[]
 * @var $productKitsById  \Model\Product\CartEntity[]
 */
?>

<?
$creditData = [];

foreach ($products as $product) {
    $cartProduct = $cartProductsById[$product->getId()];
    $creditData[] = array(
        'id'       => $product->getId(),
        'quantity' => $cartProduct->getQuantity(),
        'price'    => $cartProduct->getPrice(),
        'type'     => \Model\CreditBank\Repository::getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null),
    );
}
?>


<!-- Basket -->
<script type="text/html" id="f1cartline">
    <tr ref="<%=f1token%>">
        <td>
            <%=f1title%>
            <br/>
            <a class="bBacketServ__eMore" href="<?= $page->url('service.show', array('serviceToken' => '<%=fid%>')); ?>">Подробнее об услуге</a>
        </td>
        <td class="mPrice">
            <span class="price"><%=f1price%> </span>
            <span class="rubl">p</span>
        </td>
        <td class="mEdit">
            <div class="numerbox mInlineBlock mVAMiddle">
                <a ref="<?= $page->url('cart.service.add', array('serviceId' => 'F1ID', 'quantity' => -1, 'productId' => 'PRID')); ?>"
                   href="#">
                    <b class="ajaless" title="Уменьшить"></b>
                </a>
                <input maxlength="2" class="ajaquant" value="1"/>
                <a href="<?= $page->url('cart.service.add', array('serviceId' => 'F1ID', 'quantity' => 1, 'productId' => 'PRID')); ?>">
                    <b class="ajamore" title="Увеличить"></b>
                </a>
            </div>
            <a class="button whitelink ml5 mInlineBlock mVAMiddle"
               href="<?= $page->url('cart.service.delete', array('serviceId' => 'F1ID', 'productId' => 'PRID')); ?>">Отменить</a>
        </td>
    </tr>
</script>

<script type="text/html" id="wrntline">
    <tr ref="<%=ewid%>">
        <td>
            <span class="ew_title"><%=f1title%></span>
            <br/>
            <!--a class="bBacketServ__eMore" href="#">Подробнее об услуге</a-->
        </td>
        <td class="mPrice">
            <span class="price"><%=f1price%></span>
            &nbsp;<span class="rubl">p</span>
        </td>
        <td class="mEdit">
        <div class="numerbox">
            <b title="Уменьшить" class="ajaless"></b>
            <input value="1" class="ajaquant" maxlength="2">
            <a href="<?= $page->url('cart.warranty.set', array('warrantyId' => 'WID', 'productId' => 'PRID', 'quantity' => 1)) ?>"><b title="Увеличить" class="ajamore"></b></a>
        </div>
            <a class="button whitelink ml5 mInlineBlock mVAMiddle"
               href="<?= $page->url('cart.warranty.delete', array('warrantyId' => 'WID', 'productId' => 'PRID')) ?>">Отменить</a>
        </td>
    </tr>
</script>

<? if ($creditEnabled): ?>
    <div id="tsCreditCart" data-value='<?= json_encode($creditData) ?>'></div>
<? endif ?>


<script type="text/html" id="bKitPopupLine_Tmpl">
    <div class="bKitPopupLine clearfix">
        <div class="bKitPopupLine_eImg fl"><img src="<%=image%>" alt="<%=name%>"/></div>
        <div class="bKitPopupLine_eName fl"><%=name%></div>
        <div class="bKitPopupLine_ePrice fl"><%=price%> <span class="rubl">p</span></div>
        <div class="bKitPopupLine_eQuan fl"><%=quantity%> шт.</div>
    </div>
</script>
<div id="kitPopup" class="popup">
    <a class="close" href="#">Закрыть</a>
    <div class="bKitPopup">
        
    </div>
</div>

<? foreach ($products as $product): ?>
<?
    $cartProduct = $cartProductsById[$product->getId()];
?>
    <div class="basketline mWrap" ref="<?= $product->getId() ?>">
        <div class="basketleft">
            <a href="<?= $product->getLink() ?>">
                <img src="<?= $product->getImageUrl() ?>" alt="<?= $product->getName() ?>" />
            </a>
        </div>
        <div class="basketright clearfix">
            <div class="goodstitle">
                <div class="font24 pb5"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
                <? if ($cartProduct->getIsBuyable()): ?>
                    <noindex>
                        <div class="font11">Есть в наличии</div>
                    </noindex>
                <? else: ?>
                    <noindex>
                        <div class="font11">Нет в наличии</div>
                    </noindex>
                <? endif ?>
            </div>
            <div class="basketinfo pb15">
                <div class="left font11">&nbsp;</div>
                <div class="right">
                    <?= $page->render('_spinner', array(
                        'quantity' => $cartProduct->getQuantity(),
                        'incUrl'   => $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => 1)),
                        'decUrl'   => $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => -1)),
                    ))?>
                </div>
            </div>
            <div class="basketinfo">
                <div class="left font24"><span class="sum"><?= $page->helper->formatPrice($cartProduct->getSum()) ?></span> <span class="rubl">p</span></div>
                <div class="right">
                    <a href="<?= $page->url('cart.product.delete', array('productId' => $product->getId())) ?>" class="button whitelink mr5">Удалить</a>
                </div>
            </div>

            <?
                $kitData = [];
                foreach ($product->getKit() as $kit) {
                    $productKit = isset($productKitsById[$kit->getId()]) ? $productKitsById[$kit->getId()] : null;
                    if (!$productKit) {
                        \App::logger()->error(sprintf('Не загружен товар для элемента набора #%s', $kit->getId()));
                        continue;
                    }

                    $kitData[] = array(
                        'name'     => $productKit->getName(),
                        'image'    => $productKit->getImageUrl(0),
                        'price'    => $productKit->getPrice(),
                        'quantity' => $kit->getCount() * $cartProduct->getQuantity(),
                    );
                }
            ?>
            <div class="clear pb15"></div>

            <? if ((bool)$product->getService()): ?>
                <?= $page->render('cart/_serviceByProduct', array('product' => $product, 'cartProduct' => $cartProduct)) ?>
            <?endif ?>

            <? if (\App::config()->warranty['enabled'] && (bool)$product->getWarranty()): ?>
                <?= $page->render('cart/_warrantyByProduct', array('product' => $product, 'cartProduct' => $cartProduct)) ?>
            <?endif ?>

            <? if ((bool)$kitData): ?>
                <a id="<?= sprintf('product-%s-kit', $product->getId()) ?>" href="#" class="product_kit-data fr mt15 button whitelink" data-value="<?= $page->json($kitData) ?>">Посмотреть состав набора</a>
            <? endif ?>

        </div>
    </div>
<? endforeach ?>


<? foreach ($services as $service): ?>
    <?
    /** @var $cartService \Model\Cart\Service\Entity */
    $cartService = $cartServicesById[$service->getId()];
    ?>

    <div class="basketline mWrap">
        <div class="basketleft">
            <a href="<?= $page->url('service.show', array('serviceToken' => $service->getToken())) ?>">
                <? if ($service->getImageUrl()): ?>
                    <img src="<?= $service->getImageUrl(2) ?>" alt="<?= $service->getName() ?>" />
                <? else: ?>
                    <div class="bServiceCard__eLogo_free pr_imp"></div>
                <? endif ?>
            </a>
        </div>
        <div class="basketright">
            <div class="goodstitle">
                <div class="font24 pb5">
                    <a href="<?= $page->url('service.show', array('serviceToken' => $service->getToken())) ?>"><?= $service->getName() ?></a>
                </div>
                <? if ($cartService->getIsBuyable()): ?>
                    <noindex>
                        <div class="font11">Есть в наличии</div>
                    </noindex>
                <? else: ?>
                    <noindex>
                        <div class="font11">Нет в наличии</div>
                    </noindex>
                <? endif ?>
            </div>
            <div class="basketinfo pb15">
                <div class="left font11">
                    Цена:<br/><span class="font12">
                    <span class="price"><?= $service->getPrice() ? $page->helper->formatPrice($service->getPrice()) : '' ?></span> <span class="rubl">p</span></span>
                </div>
                <div class="right">
                    <?= $page->render('_spinner', array(
                        'quantity' => $cartService->getQuantity(),
                       'incUrl'   => $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => 0, 'quantity' => 1)),
                       'decUrl'   => $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => 0, 'quantity' => -1)),
                    ))?>
                </div>
            </div>
            <div class="basketinfo">
                <div class="left font24"><span class="sum"><?= $page->helper->formatPrice($cartService->getSum()) ?></span> <span class="rubl">p</span></div>
                <div class="right">
                    <a href="<?= $page->url('cart.service.delete', array('serviceId' => $service->getId(), 'productId' => 0)) ?>" class="button whitelink mr5">Удалить</a>
                </div>
            </div>

            <div class="clear pb15"></div>

        </div>
    </div>
<? endforeach ?>
<!-- /Basket -->
