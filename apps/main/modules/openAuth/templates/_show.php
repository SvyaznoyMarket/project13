<script src="http://vkontakte.ru/js/api/openapi.js?3"></script>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/ru_RU/all.js"></script>

<ul id="open_auth-block" class="inline">
<?php foreach ($list as $item): ?>
  <li><a id="open_auth_<?php echo $item['token'] ?>-link" class="open_auth-link" href="<?php echo $item['url'] ?>" <?php foreach($item['data'] as $k => $v) echo 'data-'.$k.'="'.$v.'"' ?> target="_blank"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>