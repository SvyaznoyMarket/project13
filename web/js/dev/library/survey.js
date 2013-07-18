/**
 * Обработчик опроса
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var toggleSurveyBox = function(){
    var toggle = this;
    var width_diff = 120;
    var height_diff = 300;

    if($('.surveyBox').hasClass('expanded')) {
      $('.surveyBox').animate({
        width: '-='+width_diff,
        height: '-='+height_diff
      }, 250, function() {
        $(toggle).html('Показать опрос');
        $('.surveyBox__content').hide();
        $('.surveyBox').removeClass('expanded');
      });
    } else {
      $('.surveyBox').animate({
        width: '+='+width_diff,
        height: '+='+height_diff
      }, 250, function() {
        $(toggle).html('Скрыть опрос');
        $('.surveyBox__content').show();
        $('.surveyBox').addClass('expanded');
      });
    }
    return false;
  };
  
  var submitAnswer = function(){
    var question = $('.surveyBox__question').html();
    var answer = $(this).html();
    var kmId = null;
    if(typeof(window.KM) !== 'undefined') {
      kmId = window.KM._i;
    }
    $.post('/survey/submit-answer', {question: question, answer: answer, kmId: kmId}, function() {
      $('.surveyBox').hmtl('Спасибо за ответ!');
      setTimeout(function(){$('.surveyBox').fadeOut();},2000);
    });
    return false;
  };

  var initTime = null;
  var serverTime = null;
  var showDelay = null;
  var isTimePassed = null;

  var initSurveyBoxData = function(){
    var surveyBox = $('.surveyBox');
    initTime = parseInt(surveyBox.data('init-time'), 10);
    serverTime = parseInt(surveyBox.data('server-time'), 10);
    showDelay = parseInt(surveyBox.data('show-delay'), 10);
    isTimePassed = parseInt(surveyBox.data('is-time-passed'), 10);
  };

  var trackIfShouldShow = function(){
    serverTime += 1;
    var shouldShow = false;
    if(serverTime > initTime + showDelay) {
      shouldShow = true;
    }

    if(shouldShow) {
      $('.surveyBox').fadeIn();
    } else {
      setTimeout(function(){trackIfShouldShow();},1000);
    }
  }; 

  $(document).ready(function(){
    $('.surveyBox__toggle').bind('click', toggleSurveyBox);
    $('.surveyBox__answer').bind('click', submitAnswer);
    initSurveyBoxData();

    if(!isTimePassed) {
      trackIfShouldShow();
    }
  });
}());