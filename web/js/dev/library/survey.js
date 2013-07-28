/**
 * Обработчик опроса
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
    var question = null,
        sbWidthDiff = null,
        sbHeightDiff = null,
        sbWidthDiffAfterSubmit = null,
        initTime = null,
        serverTime = null,
        showDelay = null,
        isTimePassed = null,
        questionHash = null,
        cookieName = 'survey',
        cookieNameCollapsed = 'surveyCollapsed';

    /**
     * Функция инициализации параметров опроса
     */
    var initSurveyBoxData = function() {
        var surveyBox = $('.surveyBox');
        question = $('.surveyBox__question').html();
        sbWidthDiff = parseInt( surveyBox.data('expanded-width-diff'), 10 );
        sbHeightDiff = parseInt( surveyBox.data('expanded-height-diff'), 10 );
        sbWidthDiffAfterSubmit = sbWidthDiff - 14;
        initTime = parseInt( surveyBox.data('init-time'), 10 );
        serverTime = parseInt( surveyBox.data('server-time'), 10 );
        showDelay = parseInt( surveyBox.data('show-delay'), 10 );
        isTimePassed = parseInt( surveyBox.data('is-time-passed'), 10 );
        questionHash = surveyBox.data('questionHash');
    };

    /**
     * Функция разворачивания/сворачивания опроса
     */
    var toggleSurveyBox = function(){
        var toggle = this;

        if ( $('.surveyBox').hasClass('expanded') ) {
            $('.surveyBox').animate( {
                width: '-=' + sbWidthDiff,
                height: '-=' + sbHeightDiff
            }, 250, function() {
                window.docCookies.setItem( false, cookieNameCollapsed, questionHash, 7*24*60*60, '/' );
                $(toggle).html('Показать опрос');
                $('.surveyBox__content').hide();
                $('.surveyBox').removeClass('expanded');
            } );
        } else {
            $('.surveyBox').animate( {
                width: '+=' + sbWidthDiff,
                height: '+=' + sbHeightDiff
            }, 250, function() {
                window.docCookies.removeItem( cookieNameCollapsed );
                $(toggle).html('Скрыть опрос');
                $('.surveyBox__content').show();
                $('.surveyBox').addClass('expanded');
            } );
        }
        return false;
    };

    /**
     * Функция ответа на опрос
     */
    var submitAnswer = function() {
        var answer = $(this).html(),
            kmId = null;
        if ( typeof(window.KM) !== 'undefined' ) {
            kmId = window.KM._i;
        }
        $.ajax({
            type: 'POST',
            url: '/survey/submit-answer',
            data: {
                question: question,
                answer: answer,
                kmId: kmId
            },
            success: function() {
                window.docCookies.setItem( false, cookieName, initTime, 7*24*60*60, '/' );
                $('.surveyBox__toggleWrapper').html('Спасибо за ответ!');
                $('.surveyBox__content').remove();
                $('.surveyBox').animate( {
                    width: '-=' + sbWidthDiffAfterSubmit,
                    height: '-=' + sbHeightDiff
                }, 250, function() {
                    setTimeout(function() {
                        $('.surveyBox').removeClass('expanded');
                        $('.surveyBox').fadeOut();
                    }, 2000);
                } );
            }
        });
        return false;
    };

    /**
     * Функция слежения за необходимостью показа опроса
     */
    var trackIfShouldShow = function() {
        var shouldShow = false;

        serverTime += 1;
        if ( serverTime > initTime + showDelay ) {
            shouldShow = true;
        }

        if ( shouldShow ) {
            showSurvey();
        } else {
            setTimeout(function() {
                trackIfShouldShow();
            }, 1000);
        }
    }; 

    /**
     * Функция определения поддерживать ли опрос свернутым
     */
    var keepCollapsed = function() {
        var cookieCollapsed = window.docCookies.getItem( cookieNameCollapsed );
        return cookieCollapsed === questionHash;
    }; 

    /**
     * Функция отображения панели опроса
     */
    var showSurvey = function() {
        $('.surveyBox').fadeIn();
        if( !keepCollapsed() ) {
            $('.surveyBox__toggle').click();
        }
    }; 

    $(document).ready(function() {
        initSurveyBoxData();
        $('.surveyBox__toggle').bind('click', toggleSurveyBox);
        $('.surveyBox__answer').bind('click', submitAnswer);

        if ( !isTimePassed ) {
            trackIfShouldShow();
        } else {
            setTimeout(function() {
                showSurvey();
            }, 1000);
        }
    });
}());
