const path = require('path');
const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
  devtool: 'eval-source-map',
  mode: "development",
  devServer: {
    static: '..',
    port: 8080,
    host: "localhost",
    allowedHosts: 'auto',
  },
});