<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

	<ul class="sorting_lst fl-r js-category-pagination">
	    <?= $helper->renderWithMustache('gift/category/_paginationInner', (new \View\PaginationAction())->execute(
	        $helper,
	        $pager
	    )) ?>
	</ul>

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/gift/category/_paginationInner.mustache') ?>
    </script>

<? };