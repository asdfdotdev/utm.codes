# utm.codes Developer Repository

[![Build Status](https://travis-ci.org/christopherldotcom/utm.codes.svg?branch=master)](https://travis-ci.org/christopherldotcom/utm.codes) [![codecov](https://codecov.io/gh/christopherldotcom/utm.codes/branch/master/graph/badge.svg)](https://codecov.io/gh/christopherldotcom/utm.codes) [![StackShare](https://img.shields.io/badge/tech-stack-0690fa.svg?style=flat)](https://stackshare.io/christopherl/utm-codes)

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

utm.codes uses webpack. Prerequisites include a working and reasonably up-to-date install of node and npm.

For instructions on running the build check out the [README](./_build#readme).

## Tests

For instructions on running test check out the [README](./_test#readme).

## Providing Feedback

We would be delighted if you'd submit a review of this plugin. [Click here to post a review.](https://wordpress.org/plugins/utm-dot-codes/)

If you'd like to contribute to utm.codes please reference our [code of conduct](./.github/CODE_OF_CONDUCT.md) and [contributing](./.github/CONTRIBUTING.md) guides.
