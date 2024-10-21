module.exports = {
	options: {
		implementation: require('sass'),
		silenceDeprecations: [
			'legacy-js-api',
			'import',
		],
		sourceMap: false,
	},

	dist: {
		files: [{
			expand: true,
			cwd: '<%= sourceCSSDir %>',
			src: '*.scss',
			dest: '<%= destCSSDir %>',
			ext: '<%= destCSSExt %>',
		}],
	},
};
