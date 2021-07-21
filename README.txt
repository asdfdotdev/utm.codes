=== utm.codes ===
Contributors: chrislarrycarl
Donate link: https://utm.codes/pricing/
Tags: analytics, utm codes, analytics, google analytics, campaign marketing, link generator
Requires at least: 4.7.0
Tested up to: 5.8
Requires PHP: 5.6.0
Stable tag: 1.7.6
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that makes building analytics friendly links quick and easy.

== Description ==

It's time to ditch the spreadsheets for a better way to create and manage your campaign marketing links.

utm.codes turns your WordPress admin into a link building powerhouse by making it easier to create, organize, and use your links.

For more information, videos, and helpful tips [visit the utm.codes website](https://utm.codes).

= Features =

- Easily create campaign marketing links with UTM codes for better campaign analytics
- Automatically format links in lowercase, remove spaces, and remove non alphanumeric characters for more consistent reporting
- Batch create links for all your social networks with just a click
- Search, filter, and easily find existing links using familiar WordPress admin controls
- Automatically detect url error responses to prevent creating bad links
- One click link shortening with Bitly or Rebrandly for easier link sharing
- Save notes with link for team communication, usage context, future reference, and more
- Link element API filter for custom utm code formatting
- Social network options API filter for custom batch link options
- Shortener API filter for custom link shortening
- Supports adding additional custom parameters to links for improved versatility
- Multi-user access within WordPress to share creation and management responsibilities
- See your current link count in the admin dashboard "At a Glance"
- Works with PHP 5.6, 7.0, 7.1, 7.2, 7.3, and 7.4
- 100% Free and Open Source

= Installation =

Upload the utm.codes plugin to your WordPress site, activate it, and start creating your links.

= Settings =

Configure your formatting preferences, targeted social networks, link notes, and shortening preferences, under Settings / utm.codes to enable batch creation and shorten when saving links.

= Documentation =

For additional details about installing, configuring, customizing, and using utm.codes [visit our GitHub wiki](https://github.com/asdfdotdev/utm.codes/wiki)

= Development Resources =

utm.codes is built using development code not included in the WordPress.org download because it isn't required for use. This code includes the webpack build used to create our minified CSS and JavaScript as well as PHPUnit tests to ensure support of new WordPress releases.

All source code associated with utm.codes is open source, free to use for any purpose, and released under the GPL v2.0 license. Development code, including instructions for running both the build and tests, is available at the [utm.codes GitHub repository](https://github.com/asdfdotdev/utm.codes).

You can also [browse our build history at GitHub](https://github.com/asdfdotdev/utm.codes/actions).

== Frequently Asked Questions ==

= Why is the shorten link checkbox missing when I create a link? =

Shortening links requires a shortener be configured. Configure shortening under Settings > utm.codes.

= Why is the create social links checkbox missing when I create a link?  =

Batch social link creation requires selection of social networks. Select networks under Settings > utm.codes to create links for your preferred networks when batch creating social links.

= What if I need custom parameters in my links? =

Simply add the parameter to your Link URL (e.g. https://example.com/?param=value) and utm.codes will append the generated utm code values to the end of the url when you save the link.

= What if I need custom parameter formats? =

Adding your own custom formatting is easy with an API filter. Visit our GitHub wiki for examples and more details.

= What if I want to share links on a social network your settings don't support? =

Adding your own custom social network options is easy with an API filter. Visit our GitHub wiki for examples and more details.

= What if I want to shorten my links using a service other than Bitly or Rebrandly? =

Adding your own shortener support is easy with a custom shortener class and API filter. Visit our GitHub wiki for examples and more details.

= I love this plugin. =

That isn't a question. But thank you.

== Screenshots ==

1. The links list provides easy access to search, filter, edit, and copy links
2. Shorten links just by clicking the checkbox
3. Create social links in batch just by clicking the checkbox
4. Configure settings for link format, labels, link notes, your favorite social networks, and shortener
5. See your link count in the WordPress dashboard "At a Glance"

== Changelog ==

= 1.7.6 =

- Supports WordPress v5.8
- Update dependencies

= 1.7.5 =

- Supports WordPress v5.7
- Adds important fix for rebrandly custom domain API response sanitation
- Update dependencies

= 1.7.4 =

- Supports WordPress v5.6
- Migrate from Travis-CI to GitHub Actions
- Update dependencies

= 1.7.3 =

- Supports WordPress v5.5.3
- Changes webpack build
- Update dependencies

= 1.7.2 =

- Supports WordPress v5.5
- Update dependencies

= 1.7.1 =

- Cleanup permissions
- Update dependencies
- Increment tested up to to v5.4.2

= 1.7.0 =

- Supports WordPress v5.4
- New use your custom domain when shortening with Rebrandly

= 1.6.3 =

This release doesn't add new functionality, however, it does add a bit of needed housekeeping and increments tested up to for WordPress v5.3

= 1.6.2 =
- Fix quick edit post link hide bug
- Switch tools to WP-CLI for language file generation

= 1.6.1 =
- Fix bug with custom shortener API

= 1.6.0 =
- Change Bitly API from v3 to v4
- New Rebrandly API Support for link shortening
- New API filter for custom shortener service support (See Wiki for details)
- New API filter for custom error messages (custom shortener user feedback)
- New unit tests for Bitly and Rebrandly
- New mock shortener for integration tests
- Documentation Updates

= 1.5.0 =

- New API filter for custom social network options (examples in the wiki)
- New unit tests
- Code quality updates
- Changes Publish button text to Save, remove quick edit (GitHub Issue #28)

= 1.4.0 =

- New API filter for custom link element formats (examples in the wiki)
- New Notes field - save notes with your links for contextual reference
- New link to Bitly report in links list for shortened links
- Additional Code Standards Improvements
- Fix link labels for batch social links to support security release changes

= 1.3.1 =

This release does not add new functionality, however, it does meaningfully improve utm.codes adherence to WordPress coding standards and improves the overall quality of the plugin.

= 1.3.0 =

- Supports WordPress 5.0
- Fix filter label
- Replace gulp build with webpack
- Improved test coverage

= 1.2.0 =

- Replace Goo.gl with Bitly url shortener
- New "Labels" taxonomy
- New link tester

= 1.1.0 =

- New link formatting options
- New Logo

= 1.0.1 =

Fixes bug in batch link creation where source/medium were omitted from first generated link.

= 1.0.0 =

Initial release
