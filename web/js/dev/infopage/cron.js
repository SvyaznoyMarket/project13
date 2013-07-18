/**
 * Обработчик страницы оффлайновых заданий
 *
 * @author    Trushkevich Anton
 * @requires  jQuery
 */
(function(){
	var handleLinksToggle = function() {
		var toggle = $(this);
		var linksContainer = toggle.siblings('.links_response');
		var task = $(this).data('task');
    if(toggle.hasClass('expanded')) {
			linksContainer.html('');
			toggle.html('Ссылки');
      toggle.removeClass('expanded');
    } else {
			$.get('/cron/'+task+'/links', {}, function(data){
				if (data.success === true) {
					toggle.html('Скрыть ссылки');
					linksContainer.html(data.data);
				}
			});
      toggle.addClass('expanded');
    }
    return false;
	};

	var handleCronReportStart = function() {
		var toggle = $(this);
    if(toggle.hasClass('expanded')) {
			$('#report_start_response').html('');
			toggle.html('Сгенерировать');
      toggle.removeClass('expanded');
    } else {
			$.get('/cron/report', {}, function(data){
				if (data.success === true) {
					toggle.html('Скрыть информацию');
					$('#report_start_response').html(data.data);
				}
			});
      toggle.addClass('expanded');
    }
    return false;
	};


	$(document).ready(function(){
	  $('.cron_report_start').bind('click', handleCronReportStart);
	  $('.cronLinks').bind('click', handleLinksToggle);
	});
}());


