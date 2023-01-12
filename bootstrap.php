<?php

use Smashballoon\Notices\SBNotices;

if ( ! defined( 'SB_NOTICES_PLUGIN_FILE' ) ) {
	define( 'SB_NOTICES_PLUGIN_FILE', __FILE__ );
}

function sb_notices() {
    return SBNotices::instance();
}

sb_notices();
