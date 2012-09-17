<div class="servicebanner">
    Чтобы в квартире появился новый шкаф,
    <div class="">не нужно просить</div>
    у соседа шуруповерт.
</div>
<div class="slogan">
    <strong>Доставим радость, настроим комфорт!</strong>
    Специалисты F1 привезут и соберут шкаф, повесят телевизор, куда скажете, и установят стиральную машину по всем правилам.
</div>

<?php
$num = 0;

foreach($categoryTree->getChildren() as $item): ?>
<div class="servicebox fl">
    <div class="serviceboxtop"></div>
    <a href="<?php echo $item->getLink() ?>">
        <div class="serviceboxmiddle">
            <i class="<?php echo $item->getIconClass() ?>"></i>
            <strong class="font16"><?php echo $item->getName() ?></strong>
            <?php echo $item->getDescriptionByIcon(); ?>

            <br />
            <div style="text-align: right; ">
                <a href="<?php echo $item->getLink(); ?>">
                    <strong>
                        <font color="#4FCBF4">
                            выбрать услуги >
                        </font>
                    </strong>
                </a>
            </div>
        </div>
    </a>
    <div class="serviceboxbottom"></div>
</div>
<?php $num++; if ($num%2 == 0):?>
    <div class="clear pb30"></div>
    <?php endif ?>
<?php endforeach; ?>