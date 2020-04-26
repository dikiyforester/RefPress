
// compile all .scss files
// run `grunt sass` or `grunt dist` to trigger this task
module.exports = function(grunt) {

	grunt.config('sass', {

		// global options
		options: {
			sourcemap: 'none',
		},

		// compress & create min version of styles for production use
		dist: {
			options: {
				style: 'compressed',
				loadPath: require('node-bourbon').includePaths
			},
			files: {
				'<%= globals.css %>/main.min.css': '<%= globals.scss %>/main.scss',
				'<%= globals.css %>/main-rtl.min.css': '<%= globals.scss %>/main-rtl.scss',
				'<%= globals.css %>/admin.min.css': '<%= globals.scss %>/admin.scss',
				'<%= globals.css %>/editor-style.min.css': '<%= globals.scss %>/../wporg-developer/stylesheets/editor-style.css'
			}
		},

		// create clean version of styles for dev use
		dev: {
			options: {
				style: 'expanded',
				loadPath: require('node-bourbon').includePaths
			},
			files: {
				'<%= globals.scss %>/editor-style.scss': '<%= globals.scss %>/../wporg-developer/stylesheets/editor-style.css',
				'<%= globals.scss %>/partials/autocomplete.scss': '<%= globals.scss %>/../wporg-developer/stylesheets/autocomplete.css',
				'<%= globals.scss %>/partials/awesomplete.scss': '<%= globals.scss %>/../wporg-developer/stylesheets/awesomplete.css',

				'<%= globals.css %>/main.css': '<%= globals.scss %>/main.scss',
				'<%= globals.css %>/main-rtl.css': '<%= globals.scss %>/main-rtl.scss',
				'<%= globals.css %>/admin.css': '<%= globals.scss %>/admin.scss',
				'<%= globals.css %>/editor-style.css': '<%= globals.scss %>/editor-style.scss'
			}
		}

	});


	// load the plugin
	grunt.loadNpmTasks('grunt-contrib-sass');

};
