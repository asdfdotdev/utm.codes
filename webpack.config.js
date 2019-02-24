module.exports = (env) => {
  let entryFiles = [
    "./_build/javascript/index.js",
    "./_build/styles/core.scss"
  ];

  let jsOutputFile = "utmdotcodes.min.js";
  let cssOutputFile = "utmdotcodes.min.css";

  let notifierTitle = "utm.codes Webpack Build";
  let notifierLogo = "";

  const path = require("path");
  const dashboardPlugin = require("webpack-dashboard/plugin");
  const globImporter = require("node-sass-glob-importer");
  const webpackNotifierPlugin = require("webpack-notifier");

  let cssSourceMaps = false;
  let activePlugins = [
    new webpackNotifierPlugin({
      title: notifierTitle,
      contentImage: path.join(__dirname, notifierLogo),
      alwaysNotify: true,
      skipFirstNotification: false,
      excludeWarnings: false
    })
  ];

  if (env.development) {
    cssSourceMaps = "inline";
    activePlugins.push(
      new dashboardPlugin(),
    );
  }

  return {
    entry: entryFiles,
    output: {
      path: __dirname,
      filename: "./js/" + jsOutputFile
    },
    plugins: activePlugins,
    devtool: "source-map",
    module: {
      rules: [
        {
          test: /\.css$/,
          use: [
            { loader: "css-loader" }
          ]
        },
        {
          test: /\.scss$/,
          use: [
            {
              loader: "file-loader",
              options: {
                name: cssOutputFile,
                outputPath: "./css/",
                sourceMap: true
              }
            },
            {
              loader: "postcss-loader",
              options: { sourceMap: cssSourceMaps }
            },
            {
              loader: "sass-loader",
              options: {
                sourceMap: true,
                importer: globImporter()
              }
            },
          ]
        },
        {
          test: /\.js$/,
          use: [
            { loader: "babel-loader" }
          ],
          exclude: /node_modules/
        }
      ],
    }
  }
};
