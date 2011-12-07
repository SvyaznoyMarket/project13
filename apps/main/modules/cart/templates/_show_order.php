  <?php $i = 0; foreach ($list as $item): $i++ ?>
  <?php
    if ($item['type'] == 'product') {
        $url = url_for('productCard', array('product' => $item['token_prefix'].'/'.$item['token']));
    } else {
        $url = url_for('service_show', array('service' => $item['token'] ));
    }
  ?>
        <div class="fl width685 pb20<?php if ($i > 1) echo ' pl235' ?>">
            <div class="fl width140">
                <a href="<?php echo $url; ?>">
                    <img src="<?php echo $item['photo']; ?>" alt="" width="120" height="120" />
                </a>
            </div>
            <div class="fr width545 pb20">
                <div class="fl width360">
                    <a href="<?php echo $url; ?>" class="font16">
                        <?php echo $item['name'] ?> (<?php echo $item['quantity'] ?> шт.)
                    </a>
                </div>
                <div class="fr font16">
                    <?php echo $item['price'] ?>
                    <span class="rubl">p</span>
                </div>
            </div>
        </div>
        <div class="line pb20"></div>

  <?php endforeach ?>
