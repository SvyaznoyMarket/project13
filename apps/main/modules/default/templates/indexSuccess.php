<h1>Главная</h1>

<div class="block">
  <ul>
    <li><?php echo link_to('Личный кабинет', 'user', array(), array('class' => 'user')) ?></li>
    <li><?php echo link_to('Каталог товаров', 'productCatalog', array(), array('class' => 'catalog')) ?></li>
    <li><?php echo link_to('О компании', 'default_show', array('page' => 'page-1'), array('class' => 'page')) ?></li>
    <li><?php echo link_to('Помошник', 'productHelper', array(), array('class' => 'help')) ?></li>
  </ul>
</div>
