<?php if ($productCategory->getNode()->hasChildren()): ?>
<?php include_component('productCategory', 'child_list', array('view' => 'carousel', 'productCategory' => $productCategory)) ?>
<?php else: ?>

<?php endif ?>
<div class="clear"></div>
