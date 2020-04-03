const path = require('path');
const babelConfig = require('./.babel.config.js');

const Dashboard = require('webpack-dashboard/plugin');
const IgnoreAssetsPlugin = require('ignore-assets-webpack-plugin');
const Notifier = require('webpack-notifier');
const Webpack = require('webpack');

const environment = process.env.NODE_ENV;

module.exports = {

  mode: environment,

  entry: {
    'css': [
      './_build/styles/utmdotcodes.scss'
    ],

    'utmdotcodes.min': [
      './_build/javascript/utmdotcodes.js',
    ],
  },

  output: {
    path: path.resolve(__dirname),
    filename: 'js/[name].js',
  },

  plugins: [
    new Dashboard(),
    new IgnoreAssetsPlugin({
      ignore: ['js/css.js', 'js/css.js.map']
    }),
    new Notifier({
      title: 'utm.codes Webpack Build',
      contentImage: path.join(__dirname, 'icon.png'),
      alwaysNotify: true,
      skipFirstNotification: false,
      excludeWarnings: false,
    }),
    new Webpack.DefinePlugin({
      PRODUCTION: JSON.stringify('production' === environment),
    }),
  ],

  devtool: ( 'production' === environment ? '' : 'source-map' ),

  resolve: {
    extensions: ['*', '.js', '.json', '.jsx'],
  },

  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          { loader: 'vue-style-loader' },
          { loader: 'css-loader' }
        ]
      },
      {
        test: /\.s[ac]ss$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: 'utmdotcodes.min.css',
              outputPath: './css',
              sourceMap: true
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              config: {
                path: path.resolve(__dirname, 'postcss.config.js'),
                ctx: {
                  minify: ('production' === environment),
                }
              }
            }
          },
          {
            loader: 'sass-loader',
            options: {
              implementation: require('sass'),
              sourceMap: ('development' === environment),
              sassOptions: {
                precision: 10,
              }
            }
          },
          {
            loader: 'import-glob-loader',
          },
        ]
      },
      {
        test: /\.jsx?$/,
        use: [{
          loader: 'babel-loader',
          options: babelConfig,
        }],
        exclude: [
          /node_modules/,
        ],
      },
      {
        test: /\.svg$/,
        loader: 'vue-svg-loader',
      },
      {
        test: /\.(woff(2)?|ttf|eot)(\?v=\d+\.\d+\.\d+)?$/,
        use: [{
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: './fonts'
          }
        }]
      },
    ],
  },

};
