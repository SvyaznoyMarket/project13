(function() {
	if( typeof($LAB) === 'undefined' )
		throw new Error( "Невозможно загрузить файлы JavaScript" )
	function getWithVersion( flnm ) {
//console.info(flnm)
		if( typeof( filesWithVersion[''+flnm] ) !== 'undefined' ) {			
			flnm += '?' + filesWithVersion[''+flnm]
			flnm = flnm.replace('js', 'min.js')
		}	
		return flnm
	}
	$LAB.setGlobalDefaults({ AlwaysPreserveOrder:true, UseLocalXHR:false, BasePath:"/js/"})
	.script("jquery-1.6.4.min.js")
	.script("bigjquery.min.js")
	.script("combine.js")
	.wait( function() {
		$LAB
		.script(getWithVersion('main.js'))
		.script( getWithVersion('main.js') )
		.script( getWithVersion('app.auth.js') )
		.script( getWithVersion('app.search.js') )
		.script( getWithVersion('app.region.js') )    
		.script( getWithVersion('mechanics.js') )
		.wait()
		.script( getWithVersion('dash.js') )
	})	

}());