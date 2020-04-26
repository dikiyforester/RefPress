
// creates the rtl.css header banner required for WordPress
// run `grunt dist` to trigger this task. SASS must be compiled first
// otherwise it will duplicate the banner so don't run separately
module.exports = function(grunt) {

	grunt.config('usebanner', {

		// global options
		options: {
			position: 'top',
			linebreak: true
		},

		rtl: {
			options: {
				banner: '/*!\n' +
					'Theme Name: <%= pkg.title %>\n' +
					'Description: Adds support for languages written in a Right To Left (RTL) direction.\n\n' +
					'See: https://codex.wordpress.org/Right_to_Left_Language_Support\n' +
					'*/\n\n' +
					'body {\n  direction: rtl;\n  unicode-bidi: embed;\n}\n'
			},
			files: {
				src: ['<%= globals.css %>/style-rtl.css']
			}
		},

	});


	// load the plugin
	grunt.loadNpmTasks('grunt-banner');

};
