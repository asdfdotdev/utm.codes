<p align="center" style="padding-bottom:20px"><img src="img/utm-dot-codes-logo.png" width="400"></p><p>&nbsp;</p>

[![PHPUnit Tests Nightly (8.x)](https://github.com/asdfdotdev/utm.codes/workflows/PHPUnit%20Tests%20Nightly%20(8.x)/badge.svg)](https://github.com/asdfdotdev/utm.codes/actions)
[![PHPUnit Tests (8.x)](https://github.com/asdfdotdev/utm.codes/workflows/PHPUnit%20Tests%20(8.x)/badge.svg)](https://github.com/asdfdotdev/utm.codes/actions)
[![PHPUnit Tests (Legacy 8.x)](https://github.com/asdfdotdev/utm.codes/workflows/PHPUnit%20Tests%20(Legacy%208.x)/badge.svg)](https://github.com/asdfdotdev/utm.codes/actions)

[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/rating/utm-dot-codes)](https://wordpress.org/plugins/utm-dot-codes/)
[![WordPress Plugin Active Installs](https://img.shields.io/wordpress/plugin/installs/utm-dot-codes)](https://wordpress.org/plugins/utm-dot-codes/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dm/utm-dot-codes)](https://wordpress.org/plugins/utm-dot-codes/)



Welcome to the developer repository for utm.codes, a WordPress plugin that makes creating analytics friendly marketing links quick and easy.

For more information about this plugin [visit utm.codes](https://utm.codes).

To download just the plugin (without the developer extras in this repo) check out [utm.codes on WordPress.org](https://wordpress.org/plugins/utm-dot-codes/).

**Try utm.codes Now!** Use the new "Live Preview" available at WordPress.org to try utm.codes directly in your browser. [Click here to visit WordPress.org](https://wordpress.org/plugins/utm-dot-codes/)

### Compatibility

[![WordPress Compatibility](https://img.shields.io/badge/WordPress-5.1_to_6.8-blue.svg?logo=wordpress)](https://wordpress.org/)
[![PHP Compatibility](https://img.shields.io/badge/PHP-7.1_to_8.3-%238892BF.svg?logo=php)](https://php.net/)

utm.codes is developed for, and tested with, platform versions covering [the vast majority of WordPress users](https://wordpress.org/about/stats/), including:

- WordPress 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8, 5.9, 6.0, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, and 6.8
- PHP 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, and 8.3

## Branches

**main** - Contains the latest usable code and will mirror the code available for download from WordPress.org.

**development** - This branch will at times be more experimental and may contain incomplete and/or untested ideas. Please reference latest commit messages/tags for further details. This branch should be used for development and experimentation only and not deployed to a production environment.

## Repository Contents

```
.
├── .github
├── .wordpress
├── _build
│   ├── javascript
│   └── styles
├── _test
├── assets
│   ├── css
│   └── js
├── classes
│   └── shorten
├── img
├── languages
└── test
    ├── css
    └── js
```

- **.github** - GitHub.com development resources including workflows and various documentation
- **.wordpress** - WordPress.org resources including live preview blueprint and screenshots
- **_build** - Static resource build files used to compile our javascript and stylesheet
- **_test** - PHPUnit unit, integration, and ajax test resources
- **assets** - Compiled admin stylesheet and javascript used by our plugin
- **classes** - Core classes used by our plugin
- **img** - Our awesome logo
- **languages** - Portable Object Template (.pot) file for plugin translation

## Build Process

utm.codes uses webpack. Prerequisites include a working and reasonably up-to-date install of node and npm.

For instructions on running the build check out the [README](./_build#readme).

## Tests

For instructions on running test check out the [README](./_test#readme).

## Providing Feedback

We would be delighted if you'd submit a review of this plugin. [Click here to post a review.](https://wordpress.org/plugins/utm-dot-codes/)

If you'd like to contribute to utm.codes please reference our [code of conduct](./.github/CODE_OF_CONDUCT.md) and [contributing](./.github/CONTRIBUTING.md) guides.
