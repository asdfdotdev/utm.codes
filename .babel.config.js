module.exports = {
  presets: [
    [
      "@babel/preset-env",
      {
        "modules": "commonjs",
        "useBuiltIns" : "usage",
        "corejs": "3.0.0",
      }
    ],
    [
      "@babel/preset-react",
      {
        development: process.env.NODE_ENV === "development",
      },
    ],
  ],
  plugins: [
    '@babel/plugin-transform-object-assign',
    '@babel/plugin-transform-runtime',
    '@babel/plugin-transform-modules-commonjs',
    '@babel/plugin-transform-arrow-functions',
    '@babel/plugin-transform-async-to-generator',
    '@babel/plugin-proposal-class-properties',
    '@babel/plugin-proposal-object-rest-spread',
    '@babel/plugin-proposal-export-default-from',
    [
      '@babel/plugin-proposal-decorators', { 'legacy': true }
    ]
  ]
}
