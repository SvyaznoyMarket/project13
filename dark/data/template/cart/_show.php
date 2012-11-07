<?php
/**
 * @var $page             \View\Layout
 * @var $cart             \Session\Cart
 * @var $creditEnabled    bool
 * @var $products         \Model\Product\Entity[]
 * @var $services         \Model\Product\Service\Entity[]
 * @var $cartProductsById \Model\Cart\Product\Entity[]
 * @var $cartServicesById \Model\Cart\Service\Entity[]
 */
?>

<?
$creditData = array();
?>


<!-- Basket -->
<script type="text/html" id="f1cartline">
    <tr ref="<%=f1token%>">
        <td>
            <%=f1title%>
            <br>
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
                <span class="ajaquant">1 шт.</span>
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
            <br>
            <!--a class="bBacketServ__eMore" href="#">Подробнее об услуге</a-->
        </td>
        <td class="mPrice">
            <span class="price"><%=f1price%></span>
            &nbsp;<span class="rubl">p</span>
        </td>
        <td class="mQuantity" style="font-size: 80%; padding-left: 20px;">
            <span class="quantity"><%=productQ%></span>
            &nbsp;<span>шт</span>
        </td>
        <td class="mEdit">
            <a class="button whitelink ml5 mInlineBlock mVAMiddle"
               href="<?= $page->url('cart.warranty.delete', array('warrantyId' => 'WID', 'productId' => 'PRID')) ?>">Отменить</a>
        </td>
    </tr>
</script>

<? if ($creditEnabled): ?>
    <div id="tsCreditCart" data-value="<?= json_encode($creditData) ?>"></div>
<? endif ?>

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
        <div class="basketright">
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
                <div class="left font11">Цена:<br/><span class="font12"><span
                        class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></span>
                </div>
                <div class="right">
                    <div class="numerbox">
                        <? if ($cartProduct->getQuantity() > 1): ?>
                            <a href="<?= $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => -1)) ?>"><b class="ajaless" title="Уменьшить"></b></a>
                        <? else: ?>
                            <b class="ajaless" title="Уменьшить"></b>
                        <? endif ?>
                        <span class="ajaquant"><?= $cartProduct->getQuantity() ?>шт.</span>
                        <a href="<?= $page->url('cart.product.add', array('productId' => $product->getId(), 'quantity' => 1)) ?>"><b class="ajamore" title="Увеличить"></b></a>
                    </div>
                </div>
            </div>
            <div class="basketinfo">
                <div class="left font24"><span class="sum"><?= $page->helper->formatPrice($cartProduct->getTotalPrice()) ?></span> <span class="rubl">p</span></div>
                <div class="right">
                    <a href="<?= $page->url('cart.product.delete', array('productId' => $product->getId())) ?>" class="button whitelink mr5">Удалить</a>
                </div>
            </div>

            <div class="clear pb15"></div>

            <?= $page->render('cart/_serviceByProduct', array('product' => $product, 'cartProduct' => $cartProduct)) ?>

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
                    <div class="numerbox">
                        <? if ($cartService->getQuantity() > 1): ?>
                            <a href="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => null, 'quantity' => -1)) ?>"><b class="ajaless" title="Уменьшить"></b></a>
                        <? else: ?>
                            <b class="ajaless" title="Уменьшить"></b>
                        <? endif ?>
                        <span class="ajaquant"><?= $cartService->getQuantity() ?> шт.</span>
                        <a href="<?= $page->url('cart.service.add', array('serviceId' => $service->getId(), 'productId' => null, 'quantity' => 1)) ?>"><b class="ajamore" title="Увеличить"></b></a>
                    </div>
                </div>
            </div>
            <div class="basketinfo">
                <div class="left font24"><span class="sum"><?= $page->helper->formatPrice($cartService->getTotalPrice()) ?></span> <span class="rubl">p</span></div>
                <div class="right">
                    <a href="<?= $page->url('cart.service.delete', array('serviceId' => $service->getId(), 'productId' => null)) ?>" class="button whitelink mr5">Удалить</a>
                </div>
            </div>

            <div class="clear pb15"></div>

        </div>
    </div>
<? endforeach ?>
<!-- /Basket -->
