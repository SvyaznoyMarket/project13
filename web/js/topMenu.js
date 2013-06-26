/* Top Menu */
var hoverMainMenu = false
var checkedItem = null
var pointA = {
	x: 0,
	y: 0
}
var pointB = {
	x: 0,
	y: 0
}
var pointC = {
	x: 0,
	y: 0
}
var cursorNow = {
	x: 0,
	y: 0
}

/**
 * Получение площади треугольника по координатам вершин
 * 
 * @param {object} A      верхняя вершина треугольника
 * @param {object} A.x    координата по оси x верхней вершины
 * @param {object} A.y    координата по оси y верхней вершины
 * 
 * @param {object} B      левая вершина треугольника
 * @param {object} B.x    координата по оси x левой вершины
 * @param {object} B.y    координата по оси y левой вершины
 * 
 * @param {object} C      правая вершина треугольника
 * @param {object} C.x    координата по оси x правой вершины
 * @param {object} A.y    координата по оси y правой вершины
 *
 * @return {number} S площадь треульника
 *
 * @see <a href="http://ru.wikipedia.org/wiki/%D0%A4%D0%BE%D1%80%D0%BC%D1%83%D0%BB%D0%B0_%D0%93%D0%B5%D1%80%D0%BE%D0%BD%D0%B0">Формула Герона</a>
 */
getTriangleS = function(A, B, C){
	// получение длинн сторон треугольника
	var AB = Math.sqrt(Math.pow((A.x - B.x),2)+Math.pow((A.y - B.y),2))
	var BC = Math.sqrt(Math.pow((B.x - C.x),2)+Math.pow((B.y - C.y),2))
	var CA = Math.sqrt(Math.pow((C.x - A.x),2)+Math.pow((C.y - A.y),2))

	// получение площади треугольника по формуле Герона
	var p = (AB + BC + CA)/2
	var S = Math.sqrt(p*(p-AB)*(p-BC)*(p-CA))

	return S
}

/**
 * Проверка входит ли точка в треугольник.
 * Соединяем точку со всеми вершинами и считаем площадь маленьких треугольников.
 * Если она равна площади большого треугольника, то точка входит в треугольник. Иначе не входит.
 * 
 * @param  {object} now    координаты точки, которую необходимо проверить
 * 
 * @param  {object} A      верхняя вершина большого треугольника
 * @param  {object} A.x    координата по оси x верхней вершины
 * @param  {object} A.y    координата по оси y верхней вершины
 * 
 * @param  {object} B      левая вершина большого треугольника
 * @param  {object} B.x    координата по оси x левой вершины
 * @param  {object} B.y    координата по оси y левой вершины
 * 
 * @param  {object} C      правая вершина большого треугольника
 * @param  {object} C.x    координата по оси x правой вершины
 * @param  {object} A.y    координата по оси y правой вершины
 * 
 * @return {boolean}       true - входит, false - не входит
 */
menuCheckTriangle = function(){
	var res1 = (pointA.x-cursorNow.x)*(pointB.y-A.y)-(pointB.x-pointA.x)*(pointA.y-cursorNow.y)
	var res2 = (pointB.x-cursorNow.x)*(pointC.y-pointB.y)-(pointC.x-pointB.x)*(pointB.y-cursorNow.y)
	var res3 = (pointC.x-cursorNow.x)*(pointA.y-pointC.y)-(pointA.x-pointC.x)*(pointC.y-cursorNow.y)

	if ((res1 >= 0 && res2 >= 0 && res3 >= 0)||
		(res1 <= 0 && res2 <= 0 && res3 <= 0)){
		console.info('принадлежит')
		return true
	}
	else{
		console.info('не принадлежит')
		return false
	}
}

/**
 * Отслеживание перемещения мыши по меню
 * @param  {event} e
 */
menuMove = function(e){
	console.info(e.currentTarget.nodeName)
	console.log('движение...')
	cursorNow = {
		x: e.pageX,
		y: e.pageY
	}
}

menuHoverOut = function(e){
	var now = {
		x: e.pageX,
		y: e.pageY
	}
	console.log('убираем')
	if (!menuCheckTriangle(now, pointA, pointB, pointC)){
		$('.bMainMenuLevel-1__eItem').removeClass('hover')
		hoverMainMenu = false
		$(this).trigger('mouseenter')
	}
}

activateItem = function(el){
	console.log('activate')
	checkedItem = el
	el.addClass('hover')
}

createMenuTriangle = function(el){
	// верхняя точка
	pointA = {
		x: cursorNow.x,
		y: cursorNow.y
	}
	// левый угол
	pointB = {
		x: dropMenu.offset().left,
		y: dropMenu.offset().top
	}
	// правый угол
	pointC = {
		x: dropMenu.offset().left + dropMenu.width(),
		y: dropMenu.offset().top
	}
}

checkItem = function(el){
	console.log('checkedItem')
	if (pointA.x == 0 && pointA.y == 0)
		createMenuTriangle(el)
	if (menuCheckTriangle()){
		console.log('входит')
		activateItem(el)
	}
	else{
		console.log('не входит')
		createMenuTriangle(el)
		checkedItem.removeClass('hover')
		checkedItem = el
	}
}

/**
 * Обработчик наведения на элемент меню первого уровня
 */
menuHoverIn = function(){
	console.log('handler')
	if (this != checkedItem){
		console.log('new hover')
		checkItem(this)
	}
	
}

$('.bMainMenuLevel-2__eItem').mouseenter(menuHoverIn)
$('.bMainMenuLevel-2').mousemove(menuMove)

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
