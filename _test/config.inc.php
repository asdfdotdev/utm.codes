<?php
/**
 * The basic environment variables required by utm.codes unit tests, this can be copied
 * to config.inc.local.php and customized for individual environments.
 *
 * @environment Default
 *
 * @package UtmDotCodes
 */

// Absolute path to your WordPress tests directory.
putenv( 'WP_TEST_DIR=' );

// Absolute path to plugin root directory.
putenv( 'UTMDC_PLUGIN_DIR=' );

// A valid Bitly API Generic Access Token.
putenv( 'UTMDC_BITLY_API=' );

// A valid Rebrandly API Key.
putenv( 'UTMDC_REBRANDLY_API=' );
