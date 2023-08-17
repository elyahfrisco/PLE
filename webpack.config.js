const path = require("path");
// var webpack = require('webpack');

module.exports = {
  resolve: {
    alias: {
      "@": path.resolve("resources/js")
    }
  },
  stats: {
    children: true
  }
  // plugins: [
  //     new webpack.DefinePlugin({
  //         __VUE_PROD_DEVTOOLS__: true,
  //     }),
  // ],
};
