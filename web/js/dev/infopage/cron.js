$(document).ready(function(){
	if ( $('.hotlinksToggle').length ){
		$('.hotlinksToggle').toggle(
			function(){
				$(this).parent().parent().find('.toHide').show();
				$(this).html('Основные метки');
			},
			function(){
				$(this).parent().parent().find('.toHide').hide();
				$(this).html('Все метки');
			}
		);
	}

	if ( $('.cron_report_start').length ){
		$('.cron_report_start').toggle(
			function(){
				var span = $(this);
				$.get('/cron/report', {}, function(data){
					if ( data.success === true ) {
						console.log(data);
						span.html('Скрыть информацию');
						$('#report_start_response').html(data.data);
					}
				});
			},
			function(){
				$('#report_start_response').html('');
				$(this).html('Сгенерировать');
			}
		);
	}

	if ( $('.cron_report_links').length ){
		$('.cron_report_links').toggle(
			function(){
				var span = $(this);
				$.get('/cron/report/links', {}, function(data){
					if ( data.success === true ) {
						span.html('Скрыть ссылки');
						$('#report_links_response').html(data.data);
					}
				});
			},
			function(){
				$('#report_links_response').html('');
				$(this).html('Ссылки');
			}
		);
	}
});