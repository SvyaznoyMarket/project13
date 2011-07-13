<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<h1>Адреса доставки</h1>

<div class="block">
  <?php if (count($userAddressList) > 0): ?>
    <?php include_component('userAddress', 'list', array('userAddressList' => $userTagList)) ?>

  <?php else: ?>
    <p>нет адресов</p>

  <?php endif ?>

  <div class="block-inline">
    <?php include_component('userAddress', 'form') ?>
  </div>
</div>
