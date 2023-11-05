const path = require('path');

module.exports = {
  entry: ['babel-polyfill', './src/index.js'],
  output: {
    filename: 'estimates.js',
    path: path.resolve(__dirname, '..')
  },
  module: {
    rules: [
      { 
        test: /\.(js|jsx)$/, 
        exclude: /node_modules/, 
        use: "babel-loader" 
      },
    ]
  },
};