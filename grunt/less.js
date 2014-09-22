module.exports = {

    // компиляция LESS
    compile: {
        options: {
            paths: ['web/css/']
        },
        files: {
            'web/css/global.css': ['web/css/global.less']
        }
    },

    // компиляция и минификация LESS
    compress: {
        options: {
            paths: ['web/css/'],
                compress: true
        },
        files: {
            'web/css/global.min.css': ['web/css/global.less']
        }
    },

    // компиляция LESS
    compileNew: {
        options: {
            paths: ['web/styles/']
        },
        files: {
            'web/styles/global.css': ['web/styles/global.less']
        }
    },
    // компиляция и минификация LESS
    compressNew: {
        options: {
            paths: ['web/styles/'],
                compress: true
        },
        files: {
            'web/styles/global.min.css': ['web/styles/global.less']
        }
    },
	
	photoContestCompile: {
		options: {
			paths: ['web/css/photoContest/']
		},
		files: {
			'web/css/photoContest/style.css': ['web/css/photoContest/style.less']
		}
	},
	
	photoContestCompress: {
		options: {
			paths: ['web/css/photoContest/'],
			compress: true
		},
		files: {
			'web/css/photoContest/style.min.css': ['web/css/photoContest/style.less']
		}
	},
	
	
	gameSlotsCompile: {
		options: {
			paths: ['web/styles/game/slots']
		},
		files: {
			'web/styles/game/slots/style.css': ['web/styles/game/slots/style.less']
		}
	},
	
	gameSlotsCompress: {
		options: {
			paths: ['web/styles/game/slots'],
			compress: true
		},
		files: {
			'web/styles/game/slots/style.min.css': ['web/styles/game/slots/style.less']
		}
	}
};