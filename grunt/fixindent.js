
// replace space indentation with a tab
// run `grunt fixindent` or `grunt dist` to trigger this task
module.exports = function(grunt) {

	grunt.config('fixindent', {

		// global options
		options: {
			style: 'tab',
			size: 1
		},

		dist: {
			src: [
			'<%= globals.css %>/**/*.css',
			'!<%= globals.css %>/**/*.min.css',
			],
			dest: '<%= globals.css %>/'
		}
	});


	// load the plugin
	grunt.loadNpmTasks('grunt-fixindent');

};
