<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

	<ul class="sorting_lst fl-r js-category-pagination">
	    <?= $helper->renderWithMustache('gift/category/_pagination', (new \View\PaginationAction())->execute(
	        $helper,
	        $pager
	    )) ?>
	</ul>

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/gift/category/_pagination.mustache') ?>
    </script>

<? };