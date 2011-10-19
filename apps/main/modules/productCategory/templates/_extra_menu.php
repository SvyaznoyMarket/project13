<?php foreach ($rootlist as $i => $list): 
    $c = count($list);
    $iInCol = 0;
    ?>
    <div class="extramenu" style="display: none;" id="extramenu-root-<?php echo $i ?>">
        <i class="corner" style="left:290px"></i>
        <dl>
            <?php 
            $countList = array();
            foreach ($list as $i => $item){
                $countList[] = $item;    
            }
           // print_r($countList);
            ?>                    
            <?php foreach ($list as $ii => $item): ?>
            <?            // echo get_class($item);  ?>
                <?php if ($ii == 0 && $item['level'] != 1) continue; ?>
                <?php if ($item['level'] == 1 && ((isset($list[$ii+1]) && $list[$ii+1]['level'] == 1) || !isset($list[$ii+1]))) continue; ?>
                <?php $iInCol++; ?>
                <?php if ($item['level'] == 1 && $iInCol >= ceil($c/4)): $iInCol = $iInCol-ceil($c/4); ?></dl><dl><?php endif ?>

                <?php if ($item['level'] == 1): ?>
                    <dt><a href="<?php echo url_for('productCatalog_category', $item) ?>"><?php echo $item ?></a></dt>
                <?php else: ?>
                    <dd><a href="<?php echo url_for('productCatalog_category', $item) ?>"><?php echo $item ?></a></dd>
                <?php endif ?>
            <?php endforeach ?>
        </dl>

       <div class="clear"></div>
    </div>
<?php endforeach ?>



<!--   <div class="line pb5"></div>
   <ul>
	   <li><div class="pb5"><strong class="font12">Нужна помощь в выборе?</strong></div><a href="#" class="underline">Смотрите больше советов для товаров для дома</a></li>
	   <li><a href="#"><img src="/images/photo57.jpg" alt="" width="28" height="28" />Как правильно выбрать сковородку?</a></li>
	   <li><a href="#"><img src="/images/photo58.jpg" alt="" width="28" height="28" />Украшаем ванну на ваш вкус</a></li>
	   <li><a href="#"><img src="/images/photo59.jpg" alt="" width="28" height="28" />Какие средства для уборки вам подходят?</a></li>
   </ul>-->