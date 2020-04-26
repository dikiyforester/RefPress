
// adds vendor prefixes to css rules using the "Can I Use" database
// run `grunt postcss` or `grunt dist` to trigger this task
module.exports = function(grunt) {

	grunt.config('postcss', {

		options: {
			processors: [
				require('autoprefixer')({
					// Uses http://caniuse.com to return all matches
					browsers: [
						'last 2 versions', // Last 2 major browser versions
						'> 10%', // Browsers that have a global usage of over 10%
					],
					cascade: false
				})
			]
		},

		dist: {
			src: [
				'<%= globals.css %>/main.css',
				'<%= globals.css %>/main-rtl.css',
				'<%= globals.css %>/admin.css'
			]
		}

	});


	// load the plugin
	grunt.loadNpmTasks('grunt-postcss');

};
