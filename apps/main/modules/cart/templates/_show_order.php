  <?php $i = 0; foreach ($list as $item): $i++ ?>
        <div class="fl width685 pb20<?php if ($i > 1) echo ' pl235' ?>">
            <div class="fl width140"><a href="<?php echo url_for('productCard', $item['product']) ?>"><img src="<?php echo $item['product']->getMainPhotoUrl(1) ?>" alt="" width="120" height="120" /></a></div>
            <div class="fr width545 pb20">
                <div class="fl width360"><a href="<?php echo url_for('productCard', $item['product']) ?>" class="font16"><?php echo $item['product']->name ?> (<?php echo $item['quantity'] ?> шт.)</a></div>
                <div class="fr font16"><?php echo $item['priceFormatted'] ?> <span class="rubl">p</span></div>
            </div>
        </div>
        <div class="line pb20"></div>


        <?php if (count($item['service'])): ?>
            <?php foreach ($item['service'] as $service): ?>
                <?php if ($service['quantity']<1) continue; ?>

                <div class="fl width685 pb20 pl235">
                    <div class="fl width140">&nbsp;</div>
                    <div class="fr width545 pb20">
                        <div class="fl width360"><a href="<?php echo url_for('service_show', array('service' => $service['token'])) ?>" class="font16"><?php echo $service['name'] ?> (<?php echo $service['quantity'] ?> шт.)</a></div>
                        <div class="fr font16"><?php echo $service['priceFormatted'] ?> <span class="rubl">p</span></div>
                    </div>
                </div>
                <div class="line pb20"></div>

            <?php endforeach; ?>
        <?php endif ?>


  <?php endforeach ?>
