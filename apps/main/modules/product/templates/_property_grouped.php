<?php if (false): ?>
<ul>
<?php foreach ($list as $item): ?>
  <li>
    <strong><?php echo $item['name'] ?></strong>:
      <ul>
        <?php foreach($item['parameters'] as $property): ?>
        <li>
          <strong><?php echo $property['name'] ?></strong>: <?php echo $property['value'] ?>
        </li>
        <?php endforeach ?>
      </ul>
  </li>
<?php endforeach ?>
</ul>
<?php endif ?>

<?php foreach ($list as $item): ?>
        <div class="pb15"><strong><?php echo $item['name'] ?></strong></div>
        <?php foreach($item['parameters'] as $property): ?>
        <div class="point">
          <div class="title"><h3><?php echo $property['name'] ?><!--b></b--></h3>
            <!--div class="pr">
              <div class="prompting"><i class="corner"></i><i class="close" title="Закрыть">Закрыть</i>
                <div class="font16 pb5">Процессор</div>
                Хочешь получать актуальную информацию по новинкам в этом
                разделе? Хочешь получать актуальную информацию по
                новинкам в этом разделе? Хочешь получать актуальную
                информацию по новинкам в этом разделе?
              </div-->
          </div>
          <div class="description"><?php echo $property['value'] ?></div>
        </div>
        <?php endforeach ?>
<?php endforeach; ?>
