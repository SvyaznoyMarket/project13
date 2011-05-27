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
