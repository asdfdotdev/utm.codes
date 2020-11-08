module.exports = require('@flickerbox/build/webpack.config')
  .output
    .path(__dirname)
    .filename('js/[name].js')
    .end()
  .entry('utmdotcodes')
    .add('./_build/styles/utmdotcodes.scss')
    .end()
  .entry('utmdotcodes')
    .add('./_build/javascript/utmdotcodes.js')
    .end()
  .toConfig();
