<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

    <!--noindex-->
    <ul class="sorting_lst fl-r js-category-pagination">
	    <?= $helper->renderWithMustache('product-category/v2/_pagination', (new \View\PaginationAction())->execute(
	        $helper,
	        $pager
	    )) ?>
	</ul>
    <!--/noindex-->

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/v2/_pagination.mustache') ?>
    </script>

<? };