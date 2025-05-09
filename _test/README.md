# utm.codes Tests

The following will help get you up and running PHPUnit tests for the utm.codes plugin.

## Contents

```
.
├── bin
└── tests
```

- **bin** - WordPress test suite install script
- **tests** - PHPUnit tests for our plugin

## Compatibility

### PHPUnit Tests

PHPUnit tests support

- PHPUnit 5 (for PHP 5.6)
- PHPUnit 6 (for PHP 7.0, 7.1, 7.2)
- PHPUnit 7 (for PHP 7.3, 7.4)
- PHPUnit 9 (for PHP 8.0, 8.1, 8.2, 8.3)

### PHP Code Sniffer

PHP Code Sniffer v3.6+, and WordPress coding standards v2.3 are recommended.

## Initial Setup

### 0. Installing Requisite Tools

If you have composer installed running `$ composer install` from the project root will set you up with the packages you need.

To get started with composer [click here for installation details](https://getcomposer.org/download/).

### 1. Installing WordPress Tests (Automated)

The install script contained in `bin` will checkout the latest test suite from WordPress and configure your test database.

> Note: The install script is included here for convenience but can also be [generated by WP-CLI](https://developer.wordpress.org/cli/commands/scaffold/plugin-tests/).

### 1b. Manual Setup

Alternatively you can [checkout the test suite](https://develop.svn.wordpress.org/trunk/) from WordPress svn and setup your database manually [in the usual manner](https://codex.wordpress.org/Installing_WordPress).

### 2. Configuring Local Environment

Update the `config.inc.php` file to your needs:

- **WP\_TEST\_DIR** - Path to the WordPress tests directory you setup in Step 1
- **UTMDC\_PLUGIN\_DIR** - Path to the utm.codes plugin you're testing
- **UTMDC\_BITLY\_API** - A valid Bitly API Generic Access Token
- **UTMDC\_REBRANDLY]_API** - A valid Rebrandly API Token

> Note: Rename to `config.inc.local.php` (ignored in .gitignore) for environment personalization.

## Running PHPUnit Tests

From the `_test` directory execute `$ ../vendor/bin/phpunit` to run the tests. This should result in output similar to:

```
Installing...
Running as single site... To run multisite, use -c tests/phpunit/multisite.xml
Not running ajax tests. To execute these, use --group ajax.
Not running ms-files tests. To execute these, use --group ms-files.
Not running external-http tests. To execute these, use --group external-http.
PHPUnit 9.6.22 by Sebastian Bergmann and contributors.

Warning:       XDEBUG_MODE=coverage (environment variable) or xdebug.mode=coverage (PHP configuration setting) has to be set
Warning:       Your XML configuration validates against a deprecated schema.
Suggestion:    Migrate your XML configuration using "--migrate-configuration"!

................................................................. 65 / 66 ( 98%)
.                                                                 66 / 66 (100%)

Time: 00:03.543, Memory: 42.50 MB

OK (66 tests, 569 assertions)
```

#### Gotchas

Occasionally a newly installed test suite can throw the error:

```
1) TestUtmDotCodesUnit::test_is_test
UnexpectedValueException: RecursiveDirectoryIterator::__construct(/tmp/wordpress//wp-content/uploads):
failed to open dir: No such file or directory
```

This occurs because WordPress creates the uploads directory at runtime the first time you run the tests. If the directory doesn't exist and you don't have permission to create it there will be an error.

This can be resolved my running `$ sudo phpunit` or by manually creating the directory within the test suite source.

Subsequent runs against the same test suite should not require sudo.

## Running Code Standards & Compatibility Tests

From the project root running the following command will test the project PHP files against the WordPress coding standard.

```
./vendor/bin/phpcs --standard=WordPress --report=summary ./index.php ./utm-dot-codes.php ./classes
```

From the project root running the following command will test utm.codes for compatibility with PHP 5.6+

```
./vendor/bin/phpcs --standard=PHPCompatibility -p --runtime-set testVersion  5.6- --report=summary ./index.php ./utm-dot-codes.php ./classes
```

## GitHub Actions

All of these tests are [run from our GitHub repo](https://github.com/asdfdotdev/utm.codes/actions) where you can browse the test history and results.

An even older history is [available at Travis CI](https://travis-ci.org/github/asdfdotdev/utm.codes/builds).
