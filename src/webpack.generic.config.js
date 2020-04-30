const path = require('path');

module.exports = {
  mode: 'production',
  entry: [
    './js/javascript.js',
    './scss/generic.scss'
  ],
  output: {
    path: path.resolve(__dirname, 'public/compiled/'),
    filename: 'js/main.js'
  },
  module: {
    rules: [
      {
        test: /\.(scss)$/,
        use: [{
          loader: 'file-loader',
          options: { outputPath: 'css/', name: '[name].min.css'}
      }, {
          loader: 'postcss-loader', // Run postcss actions
          options: {
            plugins: function () { // postcss plugins, can be exported to postcss.config.js
              return [
                require('autoprefixer')
              ];
            }
          }
        }, {
          loader: 'sass-loader' // compiles Sass to CSS
        }]
      },
    ]
  },
  // plugins: [
  //   new webpack.ProvidePlugin({
  //     $: "jquery",
  //     jquery: "jquery",
  //     "window.jQuery": "jquery",
  //     jQuery:"jquery"
  //   })
  // ]
};