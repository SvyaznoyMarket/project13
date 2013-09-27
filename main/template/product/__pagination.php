<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

	<div class="bSortingList mPager">
	    <?= $helper->renderWithMustache('_pagination', (new \View\PaginationAction())->execute(
	        $helper,
	        $pager
	    )) ?>
	</div>

    <script id="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/_pagination.mustache') ?>
    </script>

<? };