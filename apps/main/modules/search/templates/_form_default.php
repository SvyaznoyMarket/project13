<form class="search-form" action="<?php echo url_for('search') ?>" method="get">

  <?php if (!empty($searchString)): ?>
    <input type="text" class="searchtext<?php if (isset($wide)) echo ' width483' ?>" name="q" value="<?php echo $searchString ?>" />
  <?php else: ?>
    <input type="text" class="searchtext<?php if (isset($wide)) echo ' width483' ?>" name="q" value="Поиск среди 20 000 товаров" onfocus="if (this.value == 'Поиск среди 20 000 товаров') this.value = '';" onblur="if (this.value == '') this.value = 'Поиск среди 20 000 товаров';" />
  <?php endif ?>

  <input type="submit" class="searchbutton" value="Найти" title="Найти" id="try-1" />
</form>