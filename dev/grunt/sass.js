module.exports = {
	options: {
		implementation: require('sass'),
		sourceMap: false,
	},

	dist: {
		files: [{
			expand: true,
			cwd: '<%= sourceCSSDir %>',
			src: '**/*.scss',
			dest: '<%= destCSSDir %>',
			ext: '<%= destCSSExt %>',
		}],
	},
};
