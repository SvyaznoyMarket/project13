<ul>
    <?php foreach ($list as $i => $item): ?>
    <li style="margin-left: <?php echo ($item['level'] * 40) ?>px">
        <?php if (0 == $item['level']): ?>
        <strong><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></strong>
        <?php else: ?>
        <a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a>
        <?php endif ?>

        <?php if (isset($list[$i + 1]) && ($list[$i + 1]['level'] < $item['level']) && ($item['level'] < 3)): ?><br/>
        <br/><?php endif ?>
        <?php //include_component('productCatalog_', 'creator_list', array('productCategory' => $item['productCategory'])) ?>
    </li>
    <?php endforeach ?>
</ul>