
// transforms main.css from LTR to RTL and creates main-rtl.css
// run `grunt rtlcss` or `grunt build` to trigger this task
module.exports = function(grunt) {

	grunt.config('rtlcss', {

		dist: {
			options: {
				autoRename: false,
				autoRenameStrict: false,
				blacklist:{},
				clean: true,
				greedy: false,
				processUrls: false,
			},
			files: {
				'<%= globals.css %>/main-rtl.css': '<%= globals.css %>/main-rtl.css',
				'<%= globals.css %>/main-rtl.min.css': '<%= globals.css %>/main-rtl.min.css'
			}
		},

	});


	// load the plugin
	grunt.loadNpmTasks('grunt-rtlcss');

};
