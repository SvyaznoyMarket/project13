// Simple lazy loading
;$('nav').on('mouseenter', '.navsite2_i', function(){
	$(this).find('.menuImgLazy').each(function(){
		$(this).attr('src', $(this).data('src'))
	});
});

$('nav').on('mouseenter', '.navsite_i', function(){
    var
        $el = $(this),
        url = $el.data('recommendUrl'),
        xhr = $el.data('recommendXhr')
    ;

    if (url && !xhr) {
        xhr = $.get(url);
        $el.data('recommendXhr', xhr);

        xhr.done(function(response) {
            if (!response.productBlocks) return;

            var $containers = $el.find('.jsMenuRecommendation');

            $.each(response.productBlocks, function(i, block) {
                try {
                    if (!block.categoryId) return;

                    var $container = $containers.filter('[data-parent-category-id="' + block.categoryId + '"]');
                    $container.html(block.content);
                } catch (e) { console.error(e); }
            });
        });

        xhr.fail(function() {
            $el.data('recommendXhr', false);
            //$el.data('recommendXhr', true);
        });
    }
});
