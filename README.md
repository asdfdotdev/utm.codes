# utm.codes Developer Repository

[![Build Status](https://travis-ci.org/christopherldotcom/utm.codes.svg?branch=master)](https://travis-ci.org/christopherldotcom/utm.codes) [![codecov](https://codecov.io/gh/chrislarrycarl/codes.utm/branch/master/graph/badge.svg?token=rc0h4XOMwl)](https://codecov.io/gh/chrislarrycarl/codes.utm) 

This is the developer repo for utm.codes, a WordPress plugin.

For more information about this plugin [visit utm.codes](https://utm.codes).

To download just the plugin (without the developer extras in this repo) check out [utm.codes on WordPress.org](https://wordpress.org/plugins/utm-dot-codes/).

## Branches

**master** - Contains the latest usable code and will mirror the code available for download from WordPress.org.

**development** - This branch will at times be more experimental and may contain incomplete and/or untested ideas. Please reference latest commit messages/tags for further details. This branch should be used for development and experimentation only and not deployed to a production environment.

## Repository Contents

```
.
├── _build
│   ├── javascript
│   └── sass
├── _test
│   ├── bin
│   └── tests
├── classes
├── css
├── img
├── js
└── languages
```

- **_build** - Static resource build files used to compile our javascript and stylesheet files
- **_test** - PHPUnit unit, integration, and ajax test resources
- **classes** - Core classes used by our plugin
- **css** - Compiled admin stylesheet used by our plugin
- **img** - Our awesome logo
- **js** - Compiled javascript file used by our plugin
- **languages** - Portable Object Template (.pot) file for plugin translation

## Build Process

utm.codes uses a simple gulp build process to generate static resource files. Prerequsites include a working and reasonably up-to-date install of node and npm.

From the `_build` directory running `$ npm install` should get you up and running. The build process can be executed by running the `default` task in `_build/gulpfile.js`

## Running Test

For instructions on running test check out the [README](./_test/README.md).

## Providing Feedback

We would be delighted if you'd submit a review of this plugin. [Click here to post a review.](https://wordpress.org/plugins/utm-dot-codes/)

If you'd like to contribute to utm.codes please reference our [code of conduct](./.github/CODE_OF_CONDUCT.md) and [contributing](./.github/CONTRIBUTING.md) guides.
