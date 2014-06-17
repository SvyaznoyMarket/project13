(function($) {
	/**
	 * Прокрутка фоток
	 */
	$.fn.photoStreamSlider = function(options) {
		var set = $.extend({
			prev: '> .pc_prev',
			next: '> .pc_next',
			wrapper: '> .pc_stream-wrapper',		// указатель на контейнер
			body: '> .pc_stream-wrapper > ul',		// указатель на тело с элементами
			item: '> .pc_stream-wrapper > ul > li',	// указатель на элементы 
			duration: 1000,
			step: 1
		}, options || {});

		var self = this;
		
		function init() {
			
			var wrapper		= self.find(set.wrapper),
				body		= self.find(set.body),
				items		= self.find(set.item),
				
				width		= $(wrapper).innerWidth(),
				itemWidth	= $(items[0]).outerWidth(true),
				bodyWidth	= itemWidth*items.length,
				
				offset		= $(body).position().left,
				maxOffset	= bodyWidth-width
			;
			
			// задаем ширину прокручиваемой области
			$(body).width(bodyWidth);	// @todo подумать, возможно костыль, поглядеть верстку
			
			// определяем позицию активного элемента
			for(var activePos in items) {
				if($(items[activePos]).hasClass('selected')) {
					slide(parseInt(activePos));
					break;
				}
			}
			
			
			/**
			 * Handlers
			 */
			self.find(set.next).click(function(){
				slide('next');
				return false;
			});
			
			self.find(set.prev).click(function(){
				slide('prev');
				return false;
			});
			
			
			/**
			 * 
			 * @param {string|number} mod можно передать как направление шага так и номер элемента, к которому нужно добраться
			 * @returns {null}
			 */
			function slide(mod) {
				
				if(mod==='next') {
					if(offset>=maxOffset)
						return;
					
					offset += set.step*itemWidth;
					
				} else if (mod==='prev') {
					if(offset===0)
						return;
					
					offset -= set.step*itemWidth;
					
				} else if (typeof mod === 'number') {
					// Определяем состояние
					if(itemWidth*mod<width/2)
						return;
					
					offset = itemWidth*mod - width/2;
				}
				
				// проверка выходных значений тут, т.к. смещение по номеру идет без проверок
				if(offset<0) 
					offset = 0;
				else if(offset>=maxOffset)
					offset = maxOffset;
				
				
				$(body).animate({
					left: offset*-1
				},{
					duration: set.duration
//					, easing: 'easeInOutQuad'
				});
			}
		}

		if(this.length>0) {
			init();
		}
		
		return this;
	};
	
	/**
	 * Голосование
	 */
	$.fn.vote = function(options) {
		var set = $.extend({
			routeVote: '/vote/{id}?{key}',
			routeUnvote: '/unvote/{id}?{key}',
			routeSk: '/sk',
		}, options || {});
		
		var self = this;
		
		function init(){
			$(self).click(function(){
				vote(this);
				return false;
			});
		};
		
		
		function vote(el) {
			var route	= set.routeVote;
			var vote	= true;
			
			if($(el).hasClass('active')) {
				route	= set.routeUnvote;
				vote	= false;
			} 
			
			$.ajax({
				// спрашиваем ключик
				url: set.routeSk,
				success: function(data,status,xhr){
					$.ajax({
						type: "POST",
						url: route.replace(/{id}/g,$(el).attr('data-id')).replace(/{key}/g,data.result),
						headers: {
							// передаем ключик
							'X-Referer': document.location + ' ' + xhr.getResponseHeader('x-page-id')
						},
						success: function(data,status,xhr){
							if(data.result!==undefined) {
								if(vote)
									$(el).addClass('active');
								else
									$(el).removeClass('active');
								
								$(el).find('i').html(data.result.voteCounter);
							} else if(data.error!==undefined && data.error.code===403) {
								// активируем меню загрузки
								ENTER.constructors.Login().openAuth();
							}
						}
					});
				}
			});
		}
		
		if(this.length>0) {
			init();
		}
		
		return this;
	};

})(jQuery);


$(function() {
    $('#pc_photostream').photoStreamSlider({
		duration: 500,
		step: 3
	});
	
	$('.pc_vote').vote({});
});