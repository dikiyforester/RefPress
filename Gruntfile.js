/*!
 * Theme Gruntfile
 * https://arthemes.org
 * @author ArThemes
 */

'use strict';

/**
 * Grunt Module
 */
module.exports = function(grunt) {

	grunt.initConfig({

		pkg: grunt.file.readJSON( 'package.json' ),

		// set global variables
		globals: {
			type: 'wp-theme',
			textdomain: 'refpress',
			js: 'js',
			css: 'stylesheets',
			scss: 'scss',
			languages: 'languages'
		}

	});

	/**
	 * Grunt Tasks
	 */

	// load plugin configs from grunt folder
	grunt.loadTasks( 'grunt' );


	// default task when you run 'grunt' that runs every task
	grunt.registerTask( 'default', [
		'build'
	]);


	// main task to run 'grunt dist'
	grunt.registerTask( 'dist', [
		'csscomb',
		'sass:dev',
		'postcss',
		'sass:dist',
		'rtlcss',
		'usebanner',
		'uglify',
		'jsbeautifier',
		'fixindent'
	]);


	// css task to run 'grunt css'
	grunt.registerTask( 'css', [
		'csscomb',
		'sass:dev',
		'postcss',
		'sass:dist',
		'rtlcss',
		'usebanner',
		'fixindent'
	]);


	// js task to run 'grunt js'
	grunt.registerTask( 'js', [
		'uglify:theme',
		'uglify:theme_admin',
		'uglify:theme_customize',
		'jsbeautifier'
	]);


	// custom task when you run 'grunt test'
	grunt.registerTask( 'test', [
		'csslint',
		'jshint',
		'checktextdomain'
	]);


	// custom task when you run 'grunt misc'
	grunt.registerTask( 'misc', [
		'makepot'
	]);


	// custom task when you run 'grunt build'
	grunt.registerTask( 'build', [
		'dist',
		'misc',
		'test'
	]);


};
