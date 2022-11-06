module.exports = {
  options: {
    implementation: require('dart-sass'),
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
