<?php
/**
 * @var $view
 * @var $wide
 * @var $searchString
 */
?>
<?php if(isset($view) && $view == 'main'): ?>
  <form class="search-form" action="<?php echo $this->url('search.form') ?>" method="get">
    <input name="q" type="text" class="text startse" value="Поиск среди 30 000 товаров" />
    <input type="submit" class="searchbutton" value="Найти" title="Найти" />
  </form>
<?php else: ?>
<form class="search-form" action="<?php echo $this->url('search.form') ?>" method="get">
  <?php if (!empty($searchString)): ?>
    <input type="text" class="searchtext<?php if (isset($wide)) echo ' width483' ?>" name="q" value="<?php echo $searchString ?>" />
  <?php else: ?>
    <input type="text" class="searchtext<?php if (isset($wide)) echo ' width483' ?>" name="q" value="Поиск среди 30 000 товаров" onfocus="if (this.value == 'Поиск среди 30 000 товаров') this.value = '';" onblur="if (this.value == '') this.value = 'Поиск среди 30 000 товаров';" />
  <?php endif ?>
  <input type="submit" class="searchbutton" value="Найти" title="Найти" id="try-1" />
</form>
<?php endif ?>