/**
 * Общее для всех страниц
 */
+function(d){

    var searchNode = d.querySelector('.jsKnockoutSearch');

    // Debug-панель
    window.onload = function(){
        var debugPanel = document.querySelector('.jsOpenDebugPanelContent');
        if (debugPanel) {
            debugPanel.addEventListener('click', function(){
                if (modules.getState('enter.debug') == 'NOT_RESOLVED') {
                    modules.require('enter.debug', function(){})
                }
            });
        }
    };

    // Строка поиска
    if (searchNode) {
        searchNode.addEventListener('click', function(){
            modules.require('enter.search', function(){
                d.querySelector('.jsSearchInput').focus();
            })
        })
    }

}(document);