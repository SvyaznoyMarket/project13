$(document).ready(function() {
    var container = $('#product_also_bought-container');

    if (container.length) {
        $.ajax({
            url: container.data('url'),
            timeout: 20000
        }).success(function(result) {
            container.html(result);
        });
    }
})