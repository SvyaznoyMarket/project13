<h2 class="pt7">О нас</h2>
<div class="line pb10"></div>
<ul class="wishlistmenu pb15">
<?php foreach ($list as $item): ?>
  <li><a <?php if ($page->token == $item['token']) echo 'class="current" ' ?>href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>