/* Top Menu */
var menuDelayLvl1 = 300 //ms
var menuDelayLvl2 = 500 //ms
var triangleOffset = 15 //px

var lastHoverLvl1 = null
var checkedItemLvl1 = null
var hoverNowLvl1 = false

var lastHoverLvl2 = null
var checkedItemLvl2 = null

var currentMenuItemDimensions = null
var menuLevel2Dimensions = null
var menuLevel3Dimensions = null
var pointA = {x: 0,	y: 0}
var pointB = {x: 0,	y: 0}
var pointC = {x: 0,	y: 0}
var cursorNow = {x: 0, y: 0}

/**
 * Активируем элемент меню 1-го уровня
 *
 * @param  {element} el
 */
activateItemLvl1 = function(el){
	lastHoverLvl1 = new Date()
	checkedItemLvl1 = el
	$('.bMainMenuLevel-2__eItem').removeClass('hover')
	el.addClass('hover')
}

/**
 * Обработчик наведения на элемент меню 1-го уровня
 */
menuHoverInLvl1 = function(){
	var el = $(this)
	lastHoverLvl1 = new Date()
	hoverNowLvl1 = true

	setTimeout(function(){
		if(hoverNowLvl1 && (new Date() - lastHoverLvl1 > menuDelayLvl1)) {
			activateItemLvl1(el)
		}
	}, menuDelayLvl1 + 20)
}

/**
 * Обработчик ухода мыши из элемента меню 1-го уровня
 */
menuMouseLeaveLvl1 = function(){
	var el = $(this)
	el.removeClass('hover')
	hoverNowLvl1 = false
}

/**
 * Непосредственно построение треугольника. Требуется предвариательно получить нужные координаты и размеры
 */
createTriangle = function(){
	// левый угол - текущее положение курсора
	pointA = {
		x: cursorNow.x,
		y: cursorNow.y - $(window).scrollTop()
	}

	// верхний угол - левый верх меню 3го уровня минус triangleOffset
	pointB = {
		x: menuLevel3Dimensions.left - triangleOffset,
		y: menuLevel3Dimensions.top - $(window).scrollTop()
	}

	// нижний угол - левый низ меню 3го уровня минус triangleOffset
	pointC = {
		x: menuLevel3Dimensions.left - triangleOffset,
		y: menuLevel3Dimensions.top + menuLevel3Dimensions.height - $(window).scrollTop()
	}
}

/**
 * Проверка входит ли точка в треугольник.
 * Соединяем точку со всеми вершинами и считаем площадь маленьких треугольников.
 * Если она равна площади большого треугольника, то точка входит в треугольник. Иначе не входит.
 * Также точка входит в область задержки, если она попадает в прямоугольник, формируемый сдвигом треугольника
 * 
 * @param  {object} now    координаты точки, которую необходимо проверить
 * 
 * @param  {object} A      левая вершина большого треугольника
 * @param  {object} A.x    координата по оси x левой вершины
 * @param  {object} A.y    координата по оси y левой вершины
 * 
 * @param  {object} B      верхняя вершина большого треугольника
 * @param  {object} B.x    координата по оси x верхней вершины
 * @param  {object} B.y    координата по оси y верхней вершины
 * 
 * @param  {object} C      нижняя вершина большого треугольника
 * @param  {object} C.x    координата по оси x нижней вершины
 * @param  {object} C.y    координата по оси y нижней вершины
 * 
 * @return {boolean}       true - входит, false - не входит
 */
menuCheckTriangle = function(){
	var res1 = (pointA.x-cursorNow.x)*(pointB.y-pointA.y)-(pointB.x-pointA.x)*(pointA.y-cursorNow.y)
	var res2 = (pointB.x-cursorNow.x)*(pointC.y-pointB.y)-(pointC.x-pointB.x)*(pointB.y-cursorNow.y)
	var res3 = (pointC.x-cursorNow.x)*(pointA.y-pointC.y)-(pointA.x-pointC.x)*(pointC.y-cursorNow.y)

	if ((res1 >= 0 && res2 >= 0 && res3 >= 0) || (res1 <= 0 && res2 <= 0 && res3 <= 0) || (cursorNow.x >= pointB.x && cursorNow.x <= (pointB.x + triangleOffset) && cursorNow.y >= pointB.y && cursorNow.y <= pointC.y)){
		// console.info('принадлежит')
		return true
	}
	else{
		// console.info('не принадлежит')
		return false
	}
}

/**
 * Отслеживание перемещения мыши по меню 2-го уровня
 *
 * @param  {event} e
 */
menuMoveLvl2 = function(e){
	cursorNow = {
		x: e.pageX,
		y: e.pageY - $(window).scrollTop()
	}
	var el = $(this)
	if(el.attr('class') == checkedItemLvl2.attr('class')) {
		buildTriangle(el)
		lastHoverLvl2 = new Date()
	}
	checkHoverLvl2(el)
}

/**
 * Активируем элемент меню 2-го уровня, строим треугольник
 *
 * @param  {element} el
 */
activateItemLvl2 = function(el){
	checkedItemLvl2 = el
	el.addClass('hover')
	lastHoverLvl2 = new Date()
	buildTriangle(el)
}

/**
 * Обработчик наведения на элемент меню 2-го уровня
 */
menuHoverInLvl2 = function(){
	var el = $(this)
	checkHoverLvl2(el)
	el.addClass('hoverNowLvl2')

	if(lastHoverLvl2 && (new Date() - lastHoverLvl2 <= menuDelayLvl2) && menuCheckTriangle()) {
		setTimeout(function(){
			if(el.hasClass('hoverNowLvl2') && (new Date() - lastHoverLvl2 > menuDelayLvl2)) {
				checkHoverLvl2(el)
			}
		}, menuDelayLvl2 + 20)
	}
}

/**
 * Обработчик ухода мыши из элемента меню 1-го уровня
 */
menuMouseLeaveLvl2 = function(){
	var el = $(this)
	el.removeClass('hoverNowLvl2')
}

/**
 * Меню 2-го уровня
 * Если первое наведение - просто активируем
 * Иначе - проверяем условия по которым активировать
 *
 * @param  {element} el
 */
checkHoverLvl2 = function(el) {
	if (!lastHoverLvl2) {
		activateItemLvl2(el)
	} else if(!menuCheckTriangle() || (lastHoverLvl2 && (new Date() - lastHoverLvl2 > menuDelayLvl2) && menuCheckTriangle())) {
		checkedItemLvl2.removeClass('hover')
		activateItemLvl2(el)
	}
}

/**
 * Получаем все нужные координаты и размеры и строим треугольник, попадание курсора в который
 * будет определять нужна ли задержка до переключения на другой пункт меню
 *
 * @param  {element} el
 */
buildTriangle = function(el) {
	currentMenuItemDimensions = getDimensions(el)
	menuLevel2Dimensions = getDimensions(el.find('.bMainMenuLevel-3'))
	var dropMenuWidth = el.find('.bMainMenuLevel-2__eTitle')[0].offsetWidth
	menuLevel3Dimensions = {
		top: menuLevel2Dimensions.top,
		left: menuLevel2Dimensions.left + dropMenuWidth,
		width: menuLevel2Dimensions.width - dropMenuWidth,
		height: menuLevel2Dimensions.height
	}
	createTriangle()
}

/**
 * Получение абсолютных координат элемента и его размеров
 *
 * @param  {element} el
 */
getDimensions = function(el) {
		var width = $(el).width()
		var height = $(el).height()
		el = el[0]
    var x = 0
    var y = 0
    while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
        x += el.offsetLeft - el.scrollLeft
        y += el.offsetTop - el.scrollTop
        el = el.offsetParent
    }
    return { top: y, left: x, width: width, height: height }
}


$('.bMainMenuLevel-1__eItem').mouseenter(menuHoverInLvl1)
$('.bMainMenuLevel-1__eItem').mouseleave(menuMouseLeaveLvl1)

$('.bMainMenuLevel-2__eItem').mouseenter(menuHoverInLvl2)
$('.bMainMenuLevel-2__eItem').mousemove(menuMoveLvl2)
$('.bMainMenuLevel-2__eItem').mouseleave(menuMouseLeaveLvl2)





/* код ниже был закомментирован в main.js, перенес его сюда чтобы код, касающийся меню, был в одном месте */

// header_v2
// $('.bMainMenuLevel-1__eItem').bind('mouseenter', function(){
//  var menuLeft = $(this).offset().left
//  var cornerLeft = menuLeft - $('#header').offset().left + ($(this).find('.bMainMenuLevel-1__eTitle').width()/2) - 11
//  $(this).find('.bCorner').css({'left':cornerLeft})
// })

// header_v1
// if( $('.topmenu').length && !$('body#mainPage').length) {
//  $.get('/category/main_menu', function(data){
//    $('#header').append( data )
//  })
// }

// var idcm          = null // setTimeout
// var currentMenu = 0 // ref= product ID
// function showList( self ) {  
//  if( $(self).data('run') ) {
//    var dmenu = $(self).position().left*1 + $(self).width()*1 / 2 + 5
//    var punkt = $( '#extramenu-root-'+ $(self).attr('id').replace(/\D+/,'') )
//    if( punkt.length && punkt.find('dl').html().replace(/\s/g,'') != '' )
//      punkt.show()//.find('.corner').css('left', dmenu)
//  }
// }
// if( clientBrowser.isTouch ) {
//  $('#header .bToplink').bind ('click', function(){
//    if( $(this).data('run') )
//      return true
//    $('.extramenu').hide()  
//    $('.topmenu a.bToplink').each( function() { $(this).data('run', false) } )
//    $(this).data('run', true)
//    showList( this )
//    return false
//  })
// } else { 
//  $('#header .bToplink').bind( {
//    'mouseenter': function() {
//      $('.extramenu').hide()
//      var self = this       
//      $(self).data('run', true)
//      currentMenu = $(self).attr('id').replace(/\D+/,'')
//      var menuLeft = $(self).offset().left
//      var cornerLeft = menuLeft-$('#header').offset().left+($('#topmenu-root-'+currentMenu+'').width()/2)-13
//      $('#extramenu-root-'+currentMenu+' .corner').css({'left':cornerLeft})
//      idcm = setTimeout( function() { showList( self ) }, 300)
//    },
//    'mouseleave': function() {
//      var self = this

//      if( $(self).data('run') ) {
//        clearTimeout( idcm )
//        $(self).data('run',false)
//      }
//      //currentMenu = 0
//    }
//  })
// }

// $(document).click( function(e){
//  if (currentMenu) {
//    if( e.which == 1 )
//      $( '#extramenu-root-'+currentMenu+'').data('run', false).hide()
//  }
// })

// $('.extramenu').click( function(e){
//  e.stopPropagation()
// })
