var fs = require('fs'),
	compressor = require('node-minify'),
	when = require('when'),
	less = require('less')


var POINTS = {
	'jsdir': '../web/js/',
	'cssdir': '../web/css/',
	'js': '../web/js/combine.js',
	'js': '../web/js/combine.js',
	'less': '../web/css/global.less',
	'css': '../web/css/global2.css'
}	
var red   = '\033[31m';

var parser = new(less.Parser)({
    paths: [ POINTS.cssdir ] // Specify search paths for @import directives
    // filename: 'style.less' // Specify a filename, for better error messages
})
fs.readFile( POINTS.less , 'utf8', function(e, data ) { 
	parser.parse( data, function (err, tree) {
		console.log( '< CSS >')
	    if (err) { 
	    	console.log('</ CSS >')
	    	return console.error( red + 'Error processing less file '+err)
	    }
    	
	    // console.log( tree.toCSS().length )
		fs.writeFile( POINTS.cssdir , tree.toCSS(), 'utf8', function(curr, prev) {})
		console.log('OK')
		console.log('</ CSS >')
	})
})

console.log('< JS >')

var config = {}

fs.readFile( POINTS.js , 'utf8', function(e, data ) { 
	config = JSON.parse( data.replace(/^(.)*=/,'') )	
	when( procall( config ), 
		function yep(){ console.log('all files OK') }, // good
		function nope(){ console.log( red + 'error') } // wrong
	).then( reconfig )
})

function reconfig() {
	console.log( config )
	var chunk = 'window.filesWithVersion = ' + JSON.stringify( config )
	fs.writeFile( POINTS.js , chunk, 'utf8', function(curr, prev) {})
	console.log('</ JS >')
}

function procall( list ) {
	var promises = []
	for( var k in list) 
		promises.push( procfile( k ) )
	return when.all( promises )
}

function procfile( filename ) {
	var deferred = when.defer()
	var path = POINTS.jsdir + filename,
		pathmin = POINTS.jsdir + filename.replace('js','min.js')

	fs.exists( path, function(boo) { 
		
		if( !boo ) {
			console.log( red + 'no such file ', path )
			deferred.reject(new Error( 'no such file '+ path ))
			return deferred.promise
		}
		fs.stat( path ,function(a,b) {
			// console.log(  b.mtime.getTime() , config[''+filename] )
			if( b.mtime.getTime() != config[''+filename] ) {
				console.log( 'file changed, ', path )
				new compressor.minify({
				    type: 'uglifyjs',
				    fileIn: path,
				    fileOut: pathmin,
				    callback: function(err) {
				        if(err) console.log( red + err)
				    }
				})
				config[''+filename] = b.mtime.getTime()
			}
			deferred.resolve()
		})
		
	})
	return deferred.promise
}
