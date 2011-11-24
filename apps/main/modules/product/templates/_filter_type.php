<?php $limit = 8 ?>

<?php if (count($list['first'])): ?>
<dl class="bCtg">
  <dt class="bCtg__eOrange">Найдено в<br /><?php echo $firstProductCategory->variation ?></dt>
  <dd>
    <ul>
    <?php $i = 0; $count = count($list['first']); foreach ($list['first'] as $item): $i++ ?>
      <li class="bCtg__eL2<?php if ($item['selected']) echo ' mSelected' ?><?php if (!$item['selected'] && ($i > $limit)) echo ' hf' ?>">
        <a href="<?php echo $item['url'] ?>"><span><?php echo $item['name'] ?> <b><?php echo $item['count'] ?></b></span></a>
      </li>
    <?php endforeach ?>
    </ul>
  </dd>

  <?php if (count($list['first']) > $limit): ?>
  <div class="bCtg__eMore"><a href="#">еще...</a></div>
  <?php endif ?>
</dl>
<?php endif ?>

<?php if (count($list['other'])): ?>
<dl class="bCtg">
  <dt class="bCtg__eOrange">Найдено в <?php echo count($list['first']) ? 'других' : '' ?> категориях</dt>
  <dd>
    <ul>
    <?php $i = 0; $count = count($list['other']); foreach ($list['other'] as $item): $i++ ?>
      <li class="bCtg__eL2<?php if ($item['selected']) echo ' mSelected' ?><?php if (!$item['selected'] && ($i > $limit)) echo ' hf' ?>">
        <a href="<?php echo $item['url'] ?>"><span><?php echo $item['name'] ?> <b><?php echo $item['count'] ?></b></span></a>
      </li>
    <?php endforeach ?>
    </ul>
  </dd>

  <?php if (count($list['other']) > $limit): ?>
  <div class="bCtg__eMore"><a href="#">еще...</a></div>
  <?php endif ?>
</dl>
<?php endif ?>
