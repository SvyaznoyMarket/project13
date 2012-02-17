<?php use_helper('I18N') ?>

<?php if ($count): // если товары найдены ?>
  <?php if (false == $forceSearch): // если принудительный поиск не был использован ?>
    Ура! Нашли <span class="orange">&quot;<?php echo $searchString ?>&quot;</span>
    <?php echo format_number_choice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $count), $count) ?>

  <?php else: // ...иначе, если принудительный поиск использован ?>
    Вы искали <span class="orange">&quot;<?php echo $meanSearchString ?>&quot;</span> ?<br />
    Мы нашли <?php echo format_number_choice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $count), $count) ?> :)

    <?php if ($originalSearchString_quantity): // если по оригинальной поисковой фразе товары найдены ?>
      <p class="font16" style="font-family: Tahoma;">По запросу <strong><?php echo $searchString ?></strong> найдено <a href="<?php echo url_for('search', array('q' => $searchString)) ?>"><?php echo format_number_choice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $originalSearchString_quantity), $originalSearchString_quantity) ?></a></p>
    <?php else: // ...иначе, если по оригинальной поисковой фразе товары не найдены ?>
      <p class="font16" style="font-family: Tahoma;"><strong><?php echo $searchString ?></strong> мы не нашли. Уточните, пожалуйста, запрос :)</p>
    <?php endif ?>

  <?php endif ?>

<?php else: // ...иначе, если товары не найдены ?>
  Товары не найдены

<?php endif ?>
