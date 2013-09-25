/**
 * Обработчик горячих ссылок
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
  var handleHotLinksToggle = function() {
    var toggle = $(this);
    if(toggle.hasClass('expanded')) {
      toggle.parent().parent().find('.toHide').hide();
      toggle.html('Все метки');
      toggle.removeClass('expanded');
    } else {
      toggle.parent().parent().find('.toHide').show();
      toggle.html('Основные метки');
      toggle.addClass('expanded');
    }
    return false;
  };


  $(document).ready(function(){
    $('.hotlinksToggle').bind('click', handleHotLinksToggle);
  });
}());


