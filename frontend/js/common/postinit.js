/**
 * Общее для всех страниц
 */
+function(d){

    var searchNode = d.querySelector('.jsKnockoutSearch'),
        regionSelection = d.querySelector('.jsRegionSelection');

    // Debug-панель
    window.onload = function(){
        var debugPanel = d.querySelector('.jsOpenDebugPanelContent');
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

    if (regionSelection) {
        regionSelection.addEventListener('click', function(e){
            e.preventDefault();
            modules.require('enter.region', function(module){
                if (typeof module.show == 'function') {
                    module.show()
                }
            })
        })
    }

    if (document.cookie.match(/geoshop=(\d+)/) === null || !document.cookie.match(/geoshop=(\d+)/)[1]) {
        modules.require('enter.region', function(){})
    }

}(document);