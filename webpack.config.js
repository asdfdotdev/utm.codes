module.exports = env => {
  let entry_files = [
    "./_build/javascript/index.js",
    "./_build/styles/core.scss"
  ]

  let js_filename = "utmdotcodes.min.js"
  let css_filename = "utmdotcodes.min.css"

  let notifier_title = "Webpack Build"
  let notifier_logo = ""

  const Path = require("path")
  const DashboardPlugin = require("webpack-dashboard/plugin")
  const GlobImporter = require("node-sass-glob-importer")
  const WebpackNotifierPlugin = require("webpack-notifier")

  let css_sourcemaps = false
  let active_plugins = [
    new WebpackNotifierPlugin({
      title: notifier_title,
      contentImage: Path.join(__dirname, notifier_logo),
      alwaysNotify: true,
      skipFirstNotification: false,
      excludeWarnings: false
    })
  ]

  if (env.development) {
    css_sourcemaps = "inline"
    active_plugins.push(
      new DashboardPlugin(),
    );
  }

  return {
    entry: entry_files,
    output: {
      path: __dirname,
      filename: "./js/" + js_filename
    },
    plugins: active_plugins,
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
                name: css_filename,
                outputPath: "./css/",
                sourceMap: true
              }
            },
            {
              loader: "postcss-loader",
              options: { sourceMap: css_sourcemaps }
            },
            {
              loader: "sass-loader",
              options: {
                sourceMap: true,
                importer: GlobImporter()
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
}
