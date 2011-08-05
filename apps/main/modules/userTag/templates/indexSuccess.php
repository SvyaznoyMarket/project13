<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<h1>Метки товаров</h1>

<div class="block">
  <?php if (count($userTagList) > 0): ?>
    <?php include_component('userTag', 'list', array('userTagList' => $userTagList)) ?>

  <?php else: ?>
    <p>нет меток</p>

  <?php endif ?>

  <div class="block-inline">
    <?php include_component('userTag', 'form', array('form' => isset($form) ? $form : null)) ?>
  </div>
</div>
