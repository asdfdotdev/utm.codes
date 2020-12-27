const path = require('path');

module.exports = require('@flickerbox/build/webpack.config')
  .output
    .path(__dirname)
    .filename('js/[name].js')
    .end()
  .plugin('notifier')
    .use(require('webpack-notifier'), [{
      title: 'utm.codes Build',
      alwaysNotify: true,
      skipFirstNotification: false,
      excludeWarnings: false,
    }])
    .end()
  .entry('utmdotcodes')
    .add('./_build/styles/utmdotcodes.scss')
    .end()
  .entry('utmdotcodes')
    .add('./_build/javascript/utmdotcodes.js')
    .end()
  .toConfig();
