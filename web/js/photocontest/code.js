function getPasteEvent() {
    var el = document.createElement('input'),
        name = 'onpaste';
    el.setAttribute(name, '');
    return (typeof el[name] === 'function')?'paste':'input';             
}

var pasteEventName = getPasteEvent() + ".mask",
	ua = navigator.userAgent,
	iPhone = /iphone/i.test(ua),
	android=/android/i.test(ua),
	caretTimeoutId;

$.mask = {
	//Predefined character definitions
	definitions: {
		'9': "[0-9]",
		'a': "[A-Za-z]",
		'*': "[A-Za-z0-9]"
	},
	dataName: "rawMaskFn",
	placeholder: '_',
};

$.fn.extend({
	//Helper Function for Caret positioning
	caret: function(begin, end) {
		var range;

		if (this.length === 0 || this.is(":hidden")) {
			return;
		}

		if (typeof begin == 'number') {
			end = (typeof end === 'number') ? end : begin;
			return this.each(function() {
				if (this.setSelectionRange) {
					this.setSelectionRange(begin, end);
				} else if (this.createTextRange) {
					range = this.createTextRange();
					range.collapse(true);
					range.moveEnd('character', end);
					range.moveStart('character', begin);
					range.select();
				}
			});
		} else {
			if (this[0].setSelectionRange) {
				begin = this[0].selectionStart;
				end = this[0].selectionEnd;
			} else if (document.selection && document.selection.createRange) {
				range = document.selection.createRange();
				begin = 0 - range.duplicate().moveStart('character', -100000);
				end = begin + range.text.length;
			}
			return { begin: begin, end: end };
		}
	},
	unmask: function() {
		return this.trigger("unmask");
	},
	mask: function(mask, settings) {
		var input,
			defs,
			tests,
			partialPosition,
			firstNonMaskPos,
			len;

		if (!mask && this.length > 0) {
			input = $(this[0]);
			return input.data($.mask.dataName)();
		}
		settings = $.extend({
			placeholder: $.mask.placeholder, // Load default placeholder
			completed: null
		}, settings);


		defs = $.mask.definitions;
		tests = [];
		partialPosition = len = mask.length;
		firstNonMaskPos = null;

		$.each(mask.split(""), function(i, c) {
			if (c == '?') {
				len--;
				partialPosition = i;
			} else if (defs[c]) {
				tests.push(new RegExp(defs[c]));
				if (firstNonMaskPos === null) {
					firstNonMaskPos = tests.length - 1;
				}
			} else {
				tests.push(null);
			}
		});

		return this.trigger("unmask").each(function() {
			var input = $(this),
				buffer = $.map(
				mask.split(""),
				function(c, i) {
					if (c != '?') {
						return defs[c] ? settings.placeholder : c;
					}
				}),
				focusText = input.val();

			function seekNext(pos) {
				while (++pos < len && !tests[pos]);
				return pos;
			}

			function seekPrev(pos) {
				while (--pos >= 0 && !tests[pos]);
				return pos;
			}

			function shiftL(begin,end) {
				var i,
					j;

				if (begin<0) {
					return;
				}

				for (i = begin, j = seekNext(end); i < len; i++) {
					if (tests[i]) {
						if (j < len && tests[i].test(buffer[j])) {
							buffer[i] = buffer[j];
							buffer[j] = settings.placeholder;
						} else {
							break;
						}

						j = seekNext(j);
					}
				}
				writeBuffer();
				input.caret(Math.max(firstNonMaskPos, begin));
			}

			function shiftR(pos) {
				var i,
					c,
					j,
					t;

				for (i = pos, c = settings.placeholder; i < len; i++) {
					if (tests[i]) {
						j = seekNext(i);
						t = buffer[i];
						buffer[i] = c;
						if (j < len && tests[j].test(t)) {
							c = t;
						} else {
							break;
						}
					}
				}
			}

			function keydownEvent(e) {
				var k = e.which,
					pos,
					begin,
					end;

				//backspace, delete, and escape get special treatment
				if (k === 8 || k === 46 || (iPhone && k === 127)) {
					pos = input.caret();
					begin = pos.begin;
					end = pos.end;

					if (end - begin === 0) {
						begin=k!==46?seekPrev(begin):(end=seekNext(begin-1));
						end=k===46?seekNext(end):end;
					}
					clearBuffer(begin, end);
					shiftL(begin, end - 1);

					e.preventDefault();
				} else if (k == 27) {//escape
					input.val(focusText);
					input.caret(0, checkVal());
					e.preventDefault();
				}
			}

			function keypressEvent(e) {
				var k = e.which,
					pos = input.caret(),
					p,
					c,
					next;

				if (e.ctrlKey || e.altKey || e.metaKey || k < 32) {//Ignore
					return;
				} else if (k) {
					if (pos.end - pos.begin !== 0){
						clearBuffer(pos.begin, pos.end);
						shiftL(pos.begin, pos.end-1);
					}

					p = seekNext(pos.begin - 1);
					if (p < len) {
						c = String.fromCharCode(k);
						if (tests[p].test(c)) {
							shiftR(p);

							buffer[p] = c;
							writeBuffer();
							next = seekNext(p);

							if(android){
								setTimeout($.proxy($.fn.caret,input,next),0);
							}else{
								input.caret(next);
							}

							if (settings.completed && next >= len) {
								settings.completed.call(input);
							}
						}
					}
					e.preventDefault();
				}
			}

			function clearBuffer(start, end) {
				var i;
				for (i = start; i < end && i < len; i++) {
					if (tests[i]) {
						buffer[i] = settings.placeholder;
					}
				}
			}

			function writeBuffer() { input.val(buffer.join('')); }

			function checkVal(allow) {
				//try to place characters where they belong
				var test = input.val(),
					lastMatch = -1,
					i,
					c;

				for (i = 0, pos = 0; i < len; i++) {
					if (tests[i]) {
						buffer[i] = settings.placeholder;
						while (pos++ < test.length) {
							c = test.charAt(pos - 1);
							if (tests[i].test(c)) {
								buffer[i] = c;
								lastMatch = i;
								break;
							}
						}
						if (pos > test.length) {
							break;
						}
					} else if (buffer[i] === test.charAt(pos) && i !== partialPosition) {
						pos++;
						lastMatch = i;
					}
				}
				if (allow) {
					writeBuffer();
				} else if (lastMatch + 1 < partialPosition) {
					input.val("");
					clearBuffer(0, len);
				} else {
					writeBuffer();
					input.val(input.val().substring(0, lastMatch + 1));
				}
				return (partialPosition ? i : firstNonMaskPos);
			}

			input.data($.mask.dataName,function(){
				return $.map(buffer, function(c, i) {
					return tests[i]&&c!=settings.placeholder ? c : null;
				}).join('');
			});

			if (!input.attr("readonly"))
				input
				.one("unmask", function() {
					input
						.unbind(".mask")
						.removeData($.mask.dataName);
				})
				.bind("focus.mask", function() {
					clearTimeout(caretTimeoutId);
					var pos,
						moveCaret;

					focusText = input.val();
					pos = checkVal();
					
					caretTimeoutId = setTimeout(function(){
						writeBuffer();
						if (pos == mask.length) {
							input.caret(0, pos);
						} else {
							input.caret(pos);
						}
					}, 10);
				})
				.bind("blur.mask", function() {
					checkVal();
					if (input.val() != focusText)
						input.change();
				})
				.bind("keydown.mask", keydownEvent)
				.bind("keypress.mask", keypressEvent)
				.bind(pasteEventName, function() {
					setTimeout(function() { 
						var pos=checkVal(true);
						input.caret(pos); 
						if (settings.completed && pos == input.val().length)
							settings.completed.call(input);
					}, 0);
				});
			checkVal(); //Perform initial check for existing values
		});
	}
});


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
			routeVote: '/contest/vote/{id}?{key}',
			routeUnvote: '/contest/unvote/{id}?{key}',
			routeSk: '/contest/sk',
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
	
	$('.__vote').vote({});
});