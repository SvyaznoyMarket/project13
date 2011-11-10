<?php use_helper('I18N') ?>

<?php if ($count): ?>
  Ура! Нашли <span class="orange">&quot;<?php echo $searchString ?>&quot;</span>
  <?php echo format_number_choice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $count), $count) ?>

<?php else: ?>
  Товары не найдены

<?php endif ?>
