<div id="total" class="order-cart__common-total">
    <div class="order-cart__min-sum" data-bind="visible: isMinOrderSumVisible()">
        <span class="order-cart__min-sum-desc">Минимальная стоимость заказа 990 руб.</span>
    </div>
    <div class="order-cart__total">
        <span class="order-cart__sum-txt">Сумма заказа:</span>

        <div class="order-cart__total-sum">
            <span class="price" data-bind="html: window.printPrice(cart().sum())"></span>
            <span class="rubl">p</span>
        </div>
    </div>
</div>