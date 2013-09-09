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

<div class="fl width345 font14 mNoPrint">
    <? if ($creditEnabled): ?>
    <div id="creditFlag" style="display:none">
        <label class="bigcheck <? if ($selectCredit): ?> checked<? endif ?>" for="selectCredit">
            <b></b>Выбранные товары купить в кредит
            <input autocomplete="off" class="" type="checkbox" id="selectCredit"<? if ($selectCredit): ?> checked="checked"<? endif ?> name="selectCredit"/>
        </label>

        <div class="pl35 font11">
            Получение кредита происходит онлайн после оформления заказа на сайте: заполняете заявку на кредит в банк,
            и в течение нескольких минут получаете СМС о решении банка. Оригиналы документов мы привезем вместе с выбранными товарами!
        </div>
    </div>
    <? endif ?>

</div>
<div id="total" class="fr">
    <div class="left">
        <? if ($creditEnabled) : ?>
        <div id="creditSum" data-minsum="<?= \App::config()->product['minCreditPrice'] ?>" style="display:none">
            <div class="font14 width370 creditInfo pb10 grayUnderline">
                <div class="leftTitle">Сумма заказа:</div>
                <div class="font24">
                    <span class="price"><?= $page->helper->formatPrice($cart->getSum()) ?></span> <span class="rubl">p</span>
                </div>
            </div>
            <div style="display:none" id="blockFromCreditAgent">
                <div class="font14 width370 creditInfo pb10 pt10">
                    <div class="leftTitle">
                        <strong>Ежемесячный платеж<sup>*</sup>:</strong>
                    </div>
                    <div class="font24">
                        <strong>
                            <span id="creditPrice">(считаем...)</span>
                            <span class="rubl">p</span>
                        </strong>
                    </div>
                </div>
                <div class="font11 width370"><sup>*</sup> Кредит не распространяется на услуги F1 и доставку.
                    Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.
                </div>
            </div>
        </div>
        <? endif; ?>

        <div id="commonSum">
            <div class="font14">
                Сумма заказа:
            </div>

            <div class="oldPrice font18 clearfix<? if (!$cart->getOriginalSum() || (abs($cart->getOriginalSum() - $cart->getSum()) == 0)): ?> hidden<? endif ?>">
                <span id="totalOldPrice">
                    <?= $page->helper->formatPrice($cart->getOriginalSum()) ?>
                </span>
                <span class="rubl">p</span>
            </div>

            <div class="font30"><strong>
				<span class="price"><?= $page->helper->formatPrice($cart->getSum()) ?></span>
                <span class="rubl">p</span></strong></div>
        </div>
    </div>

</div>

<div class="clear pb25"></div>
<div class="line pb30"></div>
<div class="fl font14 pt10 mNoPrint">&lt; <a class="underline" href="/">Вернуться к покупкам</a></div>

<div class="width500 mNoPrint auto">
    <a href="<?= $page->url('order') ?>" class="bBigOrangeButton width345">Оформить заказ</a>
</div>
<div class="clear"></div>

<? if (\App::config()->analytics['enabled']): ?>
    <!--Трэкер "Корзина"-->
    <script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
    <!--Трэкер "Корзина"-->

    <?= $page->tryRender('cart/partner-counter/_cityads') ?>
<? endif ?>