<?php if (false): ?>
<ul>
  <?php foreach ($list as $item): ?>
    <li>
        <?php include_component('product', 'show', array('view' => 'compact', 'product' => $item)) ?>
    </li>
  <?php endforeach ?>
</ul>
<?php endif ?>
            <div class="goodslist">
  <?php $i = 0; foreach ($list as $item): $i++;?>
        <?php include_component('product', 'show', array('view' => 'compact', 'product' => $item)) ?>
              <?php if (!($i % 3)): ?>
                <div class="line"></div>
             <?php endif ?>
  <?php endforeach ?>
            </div>