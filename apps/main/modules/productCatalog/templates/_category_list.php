<ul>
<?php foreach ($list as $item): ?>
  <li style="margin-left: <?php echo ($item['level'] * 40) ?>px">
    <!--<strong><a href="<?php //echo $item['url'] ?>"><?php //echo $item['name'] ?></a></strong><br />-->
    <?php echo $item['name'] ?><br />
    <?php //include_component('productCatalog', 'creator_list', array('productCategory' => $item['productCategory'])) ?>
  </li>
<?php endforeach ?>
</ul>