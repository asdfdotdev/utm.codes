/**
 * Webpack configuration.
 */

const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserJsPlugin = require("terser-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");

const JS_DIR = path.resolve(__dirname, "_build/javascript");
const SCSS_DIR = path.resolve(__dirname, "_build/styles");

const entry = {
  utmdotcodes: [
    JS_DIR + "/utmdotcodes.js",
    SCSS_DIR + "/utmdotcodes.scss",
  ],
};

const output = {
  path: path.resolve(__dirname, 'assets/'),
  filename: "js/[name].js",
};

const plugins = (argv) => [
  new CleanWebpackPlugin({
    cleanStaleWebpackAssets: "production" === argv.mode,
  }),
  new MiniCssExtractPlugin({
    filename: "css/[name].css",
  }),
];

const rules = [
  {
    test: /\.(?:js|mjs|cjs)$/,
    include: [JS_DIR],
    exclude: /node_modules/,
    use: {
      loader: "babel-loader",
      options: {
        presets: [["@babel/preset-env", { targets: "defaults" }]],
      },
    },
  },
  {
    test: /\.scss$/,
    exclude: /node_modules/,
    use: [
      MiniCssExtractPlugin.loader,
      "css-loader",
      {
        loader: "sass-loader",
        options: {
          api: "modern",
          sassOptions: {},
        },
      },
    ],
  },
];

module.exports = (env, argv) => ({
  entry,
  output,
  devtool: "source-map",

  module: {
    rules,
  },

  optimization: {
    minimizer: [new CssMinimizerPlugin(), new TerserJsPlugin()],
    minimize: true,
  },

  plugins: plugins(argv),

  externals: {},
});
