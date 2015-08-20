/**
 * Общее для всех страниц
 */
+function(d){

    var searchNode = d.querySelector('.jsKnockoutSearch'),
        menu = d.querySelector('.js-navigation-menu-holder'),
        moduleRequireOnClick = d.querySelectorAll('.js-module-require-onclick'),
        moduleRequireOnHover = d.querySelectorAll('.js-module-require-onhover'),
        isGeoCookieEmpty = document.cookie.match(/geoshop=(\d+)/) === null || !document.cookie.match(/geoshop=(\d+)/)[1],
        menuHideTimeout;

    // Debug-панель
    window.addEventListener('load', function(){
        var debugPanel = d.querySelector('.jsOpenDebugPanelContent');
        if (debugPanel) {
            debugPanel.addEventListener('click', function(){
                if (modules.getState('enter.debug') == 'NOT_RESOLVED') {
                    modules.require('enter.debug', function(){})
                }
            });
        }
        });

    // Строка поиска
    if (searchNode) {
        searchNode.addEventListener('click', function(){
            modules.require('enter.search', function(){
                d.querySelector('.jsSearchInput').focus();
            })
        })
    }

    // загрузка модулей по клику на элементах .js-module-require-onclick
    if (moduleRequireOnClick) {
        for (var i in moduleRequireOnClick) {
            if (moduleRequireOnClick.hasOwnProperty(i) && typeof moduleRequireOnClick[i] == 'object'){
                var moduleName =  moduleRequireOnClick[i].dataset.module;
                if (moduleName) {
                    (function(module, element) {
                        moduleRequireOnClick[i].addEventListener('click', function(event){
                            console.log('require module %s from ', module, element);
                            event.preventDefault();
                            modules.require(module, function(module){
                                if (typeof module.init == 'function') module.init(element)
                            })
                        })
                    })(moduleName, moduleRequireOnClick[i]);
                }
            }
        }
    }

    // загрузка модулей по наведению на элементах .js-module-require-onhover
    if (moduleRequireOnHover) {
        for (i in moduleRequireOnHover) {
            if (moduleRequireOnHover.hasOwnProperty(i) && typeof moduleRequireOnHover[i] == 'object'){
                moduleName =  moduleRequireOnHover[i].dataset.module;
                if (moduleName) {
                    (function(module, element) {
                        moduleRequireOnHover[i].addEventListener('mouseover', function(event){
                            event.preventDefault();
                            modules.require(module, function(module){
                                if (typeof module.init == 'function') module.init(element)
                            })
                        })
                    })(moduleName, moduleRequireOnHover[i]);
                }
            }
        }
    }

    // Если не стоит куки региона
    if (isGeoCookieEmpty) {
        // Показываем меню
        if (menu && menu.classList) menu.classList.add('show');
        // Показываем выбор региона
        modules.require('enter.region', function(module){
            module.fillInput = true;
        })
    }

    // Показываем раскрытое меню в каждой новой сессии
    if (window.sessionStorage && !window.sessionStorage.getItem('menuShowed') && menu && menu.classList) {
        menu.classList.add('show');
        window.sessionStorage.setItem('menuShowed', '1');
        window.addEventListener('mousemove', function(){
            setTimeout(function(){
                menu.classList.remove('show');
            }, 500)
        })
    }

    // Поведение меню
    if (menu) {
        // скрываем после одной секунды
        menu.addEventListener('mouseleave', function(){
            menuHideTimeout = setTimeout(function(){menu.classList && menu.classList.remove('show')}, 1000);
        });
        // не скрываем, если вернулись на меню в течении секунды
        menu.addEventListener('mouseenter', function(){
            clearTimeout(menuHideTimeout);
        })
    }

    // Информация о пользователе
    modules.require('enter.user', function(data){
        if (data.user && data.user.id && d.querySelector('.js-userbar-user')) {
            d.querySelector('.js-userbar-user-link').href = '/private';
            d.querySelector('.js-userbar-user-text').innerHTML = data.user.name;
            d.querySelector('.js-userbar-user').classList.add('active');
        }
    });

    // Обработчик popup-ов
    modules.require(
        ['jQuery'],
        function($) {
            /**
             * lightbox не всегда нужен, поэтому запросим его только в случае необходимости
             */
            $('.js-popup-show').on('click', function( event ) {

                var current = $(this).data('popup');
                event.preventDefault();

                modules.require('jquery.lightbox_me', function(){
                    $('.js-popup-' + current ).lightbox_me({
                        closeSelector: '.js-popup-close'
                    });
                });

            })
        }
    );

}(document);