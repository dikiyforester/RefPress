
// combine and create min versions of all non lib js files
// run `grunt uglify` or `grunt dist` to trigger this task
module.exports = function(grunt) {

	grunt.config('uglify', {

		dist: {
			options: {
				beautify: true,
				mangle: false,
				compress: false,
				preserveComments: 'all'
			},
			files: [{
				expand: true,
				cwd: '<%= globals.js %>/../wporg-developer/js',
				src: '**/*.js',
				dest: '<%= globals.js %>'
			}]
		},

//		dist: {
//			options: {
//				report: 'gzip'
//			},
//			files: [{
//				expand: true,
//				src: [
//					'<%= globals.js %>/theme-scripts.js',
//					'<%= globals.js %>/theme-admin-scripts.js',
//					'<%= globals.js %>/customize-preview.js',
//					'<%= globals.js %>/customize-controls.js',
//				],
//				ext: '.min.js',
//				extDot: 'last'
//			}]
//		},

		// process the theme js files. combine them into one.
		theme: {
//			options: {
//				beautify: true,
//				mangle: false,
//				compress: false,
//				preserveComments: 'some'
//			},
//			files: [{
//				src: [
//					'<%= globals.js %>/src/**/*.js',
//					'!<%= globals.js %>/src/theme-admin.js',
//					'!<%= globals.js %>/src/customize-preview.js',
//					'!<%= globals.js %>/src/customize-controls.js',
//					'!<%= globals.js %>/src/**/*.min.js'
//				],
//				dest: '<%= globals.js %>/theme-scripts.js'
//			}]
		},

		// process the theme admin js file.
		theme_admin: {/*
			options: {
				beautify: true,
				mangle: false,
				compress: false,
				preserveComments: 'some'
			},
			files: [{
				src: [
					'<%= globals.js %>/src/theme-admin.js'
				],
				dest: '<%= globals.js %>/theme-admin-scripts.js'
			}]
		*/},

		// process the customizer preview js file.
		theme_customize: {
			/*options: {
				beautify: true,
				mangle: false,
				compress: false,
				preserveComments: 'some'
			},
			files: {
				'<%= globals.js %>/customize-preview.js': '<%= globals.js %>/src/customize-preview.js',
				'<%= globals.js %>/customize-controls.js': '<%= globals.js %>/src/customize-controls.js'
			}*/
		},

	});


	// load the plugin
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );

};
