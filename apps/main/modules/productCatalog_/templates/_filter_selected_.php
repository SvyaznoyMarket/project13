<?php
/**
 * @var $productFilter ProductCoreFormFilterSimple
 * @var $productCategory ProductCategoryEntity
 * @var $sf_data mixed
 */
$list = $productFilter->getSelectedList();
if(count($list)):
?>
<dd class="bSpecSel">
    <h3>Ваш выбор:</h3>
    <ul>
        <?php foreach ($list as $item): ?>
        <li>
            <a href="<?php echo $item['url'] ?>"
               title="<?php echo $item['title'] ?>"><b>x</b> <?php echo $item['name'] ?><?php echo('price' == $item['type'] ? '&nbsp;<span class="rubl">p</span>' : '') ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
    <a class="bSpecSel__eReset"
       href="<?php echo url_for('productCatalog_category', array('productCategory' => $productCategory->getTokenPrefix() ? ($productCategory->getTokenPrefix().'/'.$productCategory->getToken()) : $productCategory->getToken())) ?>">сбросить все</a>
</dd>
<?php endif; ?>