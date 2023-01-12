<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/sb-notices/notices/error.php.
 *
 * HOWEVER, on occasion SB Notices will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://smashballoon.com/doc/
 * @package SBNotices\Templates
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notice ) {
	return;
}

/**
 * Fires before the notices are displayed.
 */
do_action( 'sb_notices_before_error_notice' );

echo $notice;

/**
 * Fires after the notices are displayed.
 */
do_action( 'sb_notices_after_error_notice' );
