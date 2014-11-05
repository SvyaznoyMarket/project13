<? if (\App::config()->kissmentrics['enabled']) : ?>

    <div id="_cartKiss"
         style="display: none"
         data-cart="<?=$page->json([
             'count'    => count($cart->getProducts()),
             'price'    => $cart->getSum()]);?>"></div>

<? endif; ?>