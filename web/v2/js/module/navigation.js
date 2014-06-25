define(
    ['jquery', 'jquery.scrollTo'],
    function ($) {

        var chooseModelWrap = $('.chooseModel'),
            chooseModelMoreLink = chooseModelWrap.find('.chooseModel_moreLink'),
            chooseModelMoreBox = chooseModelWrap.find('.chooseModel_moreBox'),
            chooseModelMoreBoxDown = chooseModelWrap.find('.chooseModel_moreBox.more'),

            chooseModelMoreModel = function chooseModelMoreModel() {
                chooseModelMoreBox.slideToggle('800');
                chooseModelMoreLink.toggleClass('more');
            };
        // end of vars

        chooseModelMoreLink.click(chooseModelMoreModel);

        /**
         * Навигация сайта, показывается при клике по иконке .navIco
         */

        var navIco = $('.navIco'),
            navSite = $('.nav'),
            navSiteLeft = navSite.width(),

            fader = $('.fader'),

            navSiteItemLevel1 = navSite.find('.navList_text'),
            navSiteListLevel2 = navSite.find('.navListLevel2');
        // end of vars

        navSite.css({'left' : -navSiteLeft});
        navSiteListLevel2.hide();

        var
            /**
             * Показываем/скрываем навигацию
             */
            slideNav = function slideNav() {

                if ( navSite.css('display') == 'block' ) {
                    closeNav();
                }

                else {
                    fader.show(0);
                    navSite.stop(true, true).show(0).animate({'left' : 0},300);
                    $('html,body').addClass('noScroll');
                }

                return false;
            },

            /**
             * Показываем/скрываем навигацию второго уравня
             */
            slideNavLevel2 = function slideNavLevel2() {
                navSiteListLevel2.slideUp();

                if ( ($(this).next(navSiteListLevel2)).is(':visible') ) {
                    navSiteListLevel2.slideUp();
                    return;
                }

                $(this).next(navSiteListLevel2).stop(true, false).slideDown();

                if ($(this).parent().is(":last-child")) {
                    navSite.animate({ scrollTop: navSite[0].scrollHeight }, 1000);
                }

                return false;
            },

            /**
             * Скрываем навигацию при клике в любом месте кроме .nav
             */
            closeNav = function closeNav() {
                fader.hide(0);
                $('html,body').removeClass('noScroll');
                navSite.stop(true, true).animate({'left' : -navSiteLeft},300).hide(0);
                navSiteListLevel2.slideUp();
            };
        // end of vars

        navIco.on('click', slideNav);

        navSiteItemLevel1.on('click', slideNavLevel2);

        fader.live('click touchend', closeNav);

    }
);