<h1>Главная</h1>

<div class="block">
  <ul>
    <li><?php echo link_to('Личный кабинет', 'user') ?></li>
    <li><?php echo link_to('Каталог товаров', 'productCatalog') ?></li>
    <li><?php echo link_to('О компании', 'default_show', array('page' => 'page-1')) ?></li>
    <li><?php echo link_to('Помошник', 'productHelper') ?></li>
  </ul>
</div>
