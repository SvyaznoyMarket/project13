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
foreach($list as $item): ?>
    <div class="servicebox fl">
        <div class="serviceboxtop"></div>
            <a href="<?php echo url_for('service_list') . '/' . $item['token']; ?>">
                <div class="serviceboxmiddle">
                    <i class="icon1"></i>
                    <strong class="font16"><?php echo $item['name'] ?></strong>
                    <?php if (isset($item['description'])) echo $item['description']; ?>
                </div>
            </a>
        <div class="serviceboxbottom"></div>
    </div>    
  <?php //echo $item['name']; 
  //echo link_to($item['name'], 'service_show') ?>
<?php
$num++;
if ($num%2 == 0){
    $num = 0;
    echo '<div class="clear pb30"></div>';
}
endforeach;
?>