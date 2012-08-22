$(document).ready(function() {

    loadProductRelatedContainer($('#product_also_bought-container'));
    loadProductRelatedContainer($('#product_user-recommendation-container'));

    function loadProductRelatedContainer(container) {
        if (container.length) {
            $.ajax({
                url: container.data('url'),
                timeout: 20000
            }).success(function(result) {
                    container.html(result);
            });
        }
    }
})