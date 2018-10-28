=== utm.codes ===
Contributors: chrislarrycarl
Donate link: https://utm.codes/pricing/
Tags: analytics, utm codes, google analytics, tracking links, campaign marketing, link generator
Requires at least: 4.7.0
Tested up to: 5.0.0
Requires PHP: 5.6.0
Stable tag: 1.3.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create and manage your marketing tracking links in WordPress for better analytics.

== Description ==

Make your campaign marketing easier and more effective with WordPress. utm.codes turns your WordPress admin into your marketing links repository by making it easier to create, organize, and use your links.

For more information, videos, and helpful tips [visit the utm.codes website](https://utm.codes).

= Features =

- Easily create marketing links with UTM codes for better campaign analytics
- Automatically format links in lowercase, remove spaces, and remove non alphanumeric characters for more consistent reporting
- Batch create links for all your social networks with just a click
- Search, filter, and easily find existing links using familiar WordPress admin controls
- Link builder automatically detects url error responses to prevent creating bad links
- One click shortening with Bitly for easier link sharing
- Supports adding additional custom parameters to links for improved versatility
- Multi-user access to share creation and management responsibilities
- See your current link count in the admin dashboard "At a Glance"
- Works with PHP 5.6, 7.0, 7.1, and 7.2
- 100% Free and Open Source

= Installation =

Upload the utm.codes plugin to your WordPress site, activate it, and start creating your links.

= Settings =

Configure your targeted social networks, and shortening API key, under Settings / utm.codes to enable batch creation and auto shorten when saving links.

= Documentation =

For additional details about installing, configuring, and using utm.codes [visit our GitHub wiki](https://github.com/christopherldotcom/utm.codes/wiki)

= Development Resources =

utm.codes is built using development code not included in the WordPress.org download because it isn't required for use. This code includes the webpack build used to create our minified CSS and JavaScript as well as PHPUnit tests to ensure support of new WordPress releases.

All source code associated with utm.codes is open source, free to use for any purpose, and released under the GPL v2.0 license. Development code, including instructions for running both the build and tests, is available at the [utm.codes GitHub repository](https://github.com/christopherldotcom/utm.codes).

You can also [browse our build history at travis-ci.org](https://travis-ci.org/christopherldotcom/utm.codes).

== Frequently Asked Questions ==

= Why is the shorten link checkbox missing when I create a link? =

Shortening links requires a valid Bitly API Generic Access Token. Add your key under Settings > utm.codes.

= Why is the create social links checkbox missing when I create a link?  =

Batch social link creation requires selection of social networks. Select networks under Settings > utm.codes to create links for when batch creating social links.

= What if I need custom parameters in my links? =

Simply add the parameter to your Link URL (e.g. https://example.com/?param=value) and utm.codes will append your utm code values to the end of the url when you save the link.

== Screenshots ==

1. The links list provides easy access to search, filter, edit, and copy links
2. Shorten links just by clicking the checkbox
3. Create social links in batch just by clicking the checkbox
4. Configure settings for link format, your favorite social networks, Bitly API key, and more
5. See your link count in the WordPress dashboard "At a Glance"

== Changelog ==

= 1.3 =

- Supports WordPress 5.0
- Fix filter label
- Replace gulp build with webpack
- Improved test coverage

= 1.2 =

- Replace Goo.gl with Bitly url shortener
- New "Labels" taxonomy
- New link tester

= 1.1 =

- New link formatting options
- New Logo

= 1.0.1 =

Fixes bug in batch link creation where source/medium were omitted from first generated link.

= 1.0 =

Initial release
