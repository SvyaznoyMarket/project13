<form action="<?php echo url_for('search', array('searchString' => $searchString)) ?>" method="get">
  <input type="text" class="searchtext" name="q" value="Поиск товаров" onfocus="if (this.value == 'Поиск товаров') this.value = '';" onblur="if (this.value == '') this.value = 'Поиск товаров';"  />
  <input type="submit" class="searchbutton" value="Найти" title="Найти" id="try-1" />
</form>