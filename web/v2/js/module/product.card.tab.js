define(
    ['jquery'],
    function ($) {

		var body = $('body'),

			tabWrap = $('.js-tab-wrap'),
			tabWrapWidth = $('.js-tab-selector').width(),
			tabItem = $('.js-tabs-item'),
			posLeft = 0,

			tabList = tabWrap.find('.js-tab'),
			tab = tabList.find('.js-cont'),
			tabCount = tab.length;
		// end of vars	
	
		var 
			/*
			 * Добавление атрибутов к элементам описания товара
			 */
			addData = function addData() {
				//добавляем атрибут к табу data-tab
				var i = 0;
				tabItem.each(function() {
					var $self = $(this);

					$self.attr({
						'data-tab': i
					})

					i++;
				}); 

				//добавляем атрибут к контенту таба
				var i = 0;
				tab.each(function() {
					var $self = $(this);

					$self.attr({
						'data-desc': "tab-"+i
					})

					i++;
				}); 

			},

			/*
			 * Пересчет высоты/ширины контента табов
			 */
			tabsToggle = function tabsToggle() {
				console.log('tabsToggle');

				tabWrapWidth = $('.js-tab-selector').width();
				tabWrap.css({'height' : tab.first().height(), 'min-height' : 350 })

				tabWrap.css({'width' : tabWrapWidth})
				tab.css({'width': tabWrapWidth});	
				tabList.css({'width' : tabWrapWidth * tabCount});
				
				tabItem.removeClass('productDescTab_item-active');
				tabItem.first().addClass('productDescTab_item-active');
				tabList.stop(true, true).animate({'left' : 0});
				tabWrap.stop(true, true).animate({'height' : tab.first().height() })

				console.log(tabWrapWidth);
			},

			/*
			 * Слайдинг табов
			 */
			tabsSlide = function tabsSlide( event, inx ) {
					
				event.preventDefault();
					
				var $self = $(this),
					tabLinkId = $self.data('tab'),
					tabId = tab.filter('[data-desc="tab-'+tabLinkId+'"]');

				if ( tabLinkId == 0) {
					posLeft = 0;
				}
				else {
					posLeft = tabWrapWidth * tabLinkId;
				}
				
				$('html,body').animate({
					scrollTop: $self.offset().top - $('.header').outerHeight()}, 400, 
					function(){
                		$('html,body').clearQueue();
            		}
            	); 

				tabItem.removeClass('productDescTab_item-active');
				$self.addClass('productDescTab_item-active');
				tabList.stop(true, true).animate({'left' : -posLeft});
				tabWrap.stop(true, true).animate({'height' : tabId.height() });
			};

			
		//end of function

        addData();
	    $(window).on('resize', tabsToggle);
	    tabsToggle();
	    tabItem.on('click', tabsSlide);

    }
);