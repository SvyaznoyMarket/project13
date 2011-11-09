<?php if (count($list['first'])): ?>
<dl class="bCtg">
  <dt class="bCtg__eOrange">Найдено в<br /><?php echo $firstProductCategory->_variation ?></dt>
  <dd>
    <ul>
    <?php $i = 0; $count = count($list['first']); foreach ($list['first'] as $item): $i++ ?>
      <li<?php echo (!$item['selected'] && ($i > 8)) ? ' class="hf"' : '' ?>>
        <a href="<?php echo $item['url'] ?>"><span class="bCtg__eL1<?php if ($item['selected']) echo ' mSelected' ?>"><?php echo $item['name'] ?> <b><?php echo $item['count'] ?></b></span></a>
      </li>
    <?php endforeach ?>
    </ul>
  </dd>

  <?php if (count($list['first']) > 8): ?>
  <div class="bCtg__eMore"><a href="#">еще...</a></div>
  <?php endif ?>
</dl>
<?php endif ?>

<?php if (count($list['other'])): ?>
<dl class="bCtg">
  <dt class="bCtg__eOrange">Найдено в других категориях</dt>
  <dd>
    <ul>
    <?php foreach ($list['other'] as $i => $item): ?>
      <li<?php echo (!$item['selected'] && ($i > 8)) ? ' class="hf"' : '' ?>>
        <a href="<?php echo $item['url'] ?>"><span class="bCtg__eL1<?php if ($item['selected']) echo ' mSelected' ?>"><?php echo $item['name'] ?> <b><?php echo $item['count'] ?></b></span></a>
      </li>
    <?php endforeach ?>
    </ul>
  </dd>

  <?php if (count($list['other']) > 8): ?>
  <div class="bCtg__eMore"><a href="#">еще...</a></div>
  <?php endif ?>
</dl>
<?php endif ?>
