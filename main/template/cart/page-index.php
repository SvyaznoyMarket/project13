<?
/**
 * @var $page         \View\Cart\IndexPage
 * @var $user         \Session\User
 * @var $selectCredit bool
 * @var $products         \Model\Product\Entity[]
 * @var $services         \Model\Product\Service\Entity[]
 * @var $cartProductsById \Model\Cart\Product\Entity[]
 * @var $cartServicesById \Model\Cart\Service\Entity[]
 */
?>

<?
$cart = $user->getCart();
$creditEnabled = ($cart->getTotalProductPrice() >= \App::config()->product['minCreditPrice']) && \App::config()->payment['creditEnabled'] && !$user->getRegion()->getHasTransportCompany();
?>

<? if (\App::config()->adFox['enabled']): ?>
<div style="float:left; width: 100%; padding-bottom: 20px">
    <div id="adfox920" class="adfoxWrapper"></div>
</div>
<? endif ?>

<? require __DIR__ . '/_show.php' ?>

<?//= $page->render('cart/form-certificate') ?>

<div id="_cartKiss" style="display: none" data-cart="<?=$page->json(['count'=>(count($cart->getProducts()) + count($cart->getServices())), 'price'=>$cart->getSum()]);?>"></div>

<div class="basketLine clearfix">
    <div class="basketCredit clearfix mNoPrint">
        <? if ($creditEnabled): ?>
        <div id="creditFlag" style="display:none">
            <label class="bigcheck <? if ($selectCredit): ?> checked<? endif ?>" for="selectCredit">
                <b></b>Выбранные товары купить в кредит
                <input autocomplete="off" class="" type="checkbox" id="selectCredit"<? if ($selectCredit): ?> checked="checked"<? endif ?> name="selectCredit"/>
            </label>

            <p class="basketCredit__text">
                Получение кредита происходит онлайн после оформления заказа на сайте: заполняете заявку на кредит в банк,
                и в течение нескольких минут получаете СМС о решении банка. Оригиналы документов мы привезем вместе с выбранными товарами!
            </p>
        </div>
        <? endif ?>
    </div>

    <div id="total" class="fr" style="margin-right: 20px;">
        <? if ($creditEnabled) : ?>
        <div class="cre" id="creditSum" data-minsum="<?= \App::config()->product['minCreditPrice'] ?>" style="display:none">
            
            <div class="creditRate clearfix grayUnderline">
                <div class="creditRate__title">Сумма заказа:</div>

                <div class="creditRate__price">
                    <span class="price"><?= $page->helper->formatPrice($cart->getSum()) ?></span> <span class="rubl">p</span>
                </div>
            </div>

            <div class="creditRate clearfix" style="display:none; padding-top: 10px;" id="blockFromCreditAgent">
                <div class="creditRate__title">
                    <strong>Ежемесячный платеж *:</strong>
                </div>

                <strong class="creditRate__price">
                    <span id="creditPrice">(считаем...)</span>
                    <span class="rubl">p</span>
                </strong>

                <p class="creditRate__footer">
                    * Кредит не распространяется на услуги F1 и доставку.
                    Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.
                </p>
            </div>
        </div>
        <? endif; ?>

        <div class="basketSum" id="commonSum">
            Сумма заказа:

            <div class="basketSum__oldPrice oldPrice font18 clearfix<? if (!$cart->getOriginalSum() || (abs($cart->getOriginalSum() - $cart->getSum()) == 0)): ?> hidden<? endif ?>">
                <span id="totalOldPrice">
                    <?= $page->helper->formatPrice($cart->getOriginalSum()) ?>
                </span>
                <span class="rubl">p</span>
            </div>

            <div class="basketSum__price">
    			<span class="price"><?= $page->helper->formatPrice($cart->getSum()) ?></span>
                <span class="rubl">p</span>
            </div>
        </div>
    </div>
</div>

<div class="backShop fl mNoPrint">&lt; <a class="underline" href="/">Вернуться к покупкам</a></div>

<div class="basketBuy mNoPrint">
    <a href="<?= $page->url('order') ?>" class="bBigOrangeButton">Оформить заказ</a>
</div>

<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
    <!--Трэкер "Корзина"-->
    <script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
    <!--Трэкер "Корзина"-->

    <?= $page->tryRender('cart/partner-counter/_cityads') ?>
<? endif ?>