<?= $page->getParam('content') ?>

<? if(in_array($page->getParam('token'), [])) { ?>
  <div class="content-section__form content-section__form__friend__center pb30">
    <div class="subscribe-form__friend clearfix">
      <div class="subscribe-form__title subscribe-form__title__friend">Подари другу скидку</div>
      <input class="subscribe-form__email subscribe-form__email__friend" placeholder="Введите email друга">
      <button class="subscribe-form__btn subscribe-form__btn__friend">Подарить</button>
    </div>
  </div>
<? } ?>