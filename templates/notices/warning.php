<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/instagram-feed/notices/warning.php.
 *
 * HOWEVER, on occasion Instagram Feed Pro will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://smashballoon.com/doc/
 * @package InstagramFeed\Templates
 * @version 6.2.2
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
do_action( 'sbi_before_warning_notice' );

echo $notice;

/**
 * Fires after the notices are displayed.
 */
do_action( 'sbi_after_warning_notice' );
