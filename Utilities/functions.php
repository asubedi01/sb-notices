<?php

namespace Smashballoon\Framework;

if ( ! function_exists( 'sb_doing_it_wrong' ) ) {
	/**
	 * Wrapper for _doing_it_wrong().
	 *
	 * @param string $function Function used.
	 * @param string $message Message to log.
	 * @param string $version Version the message was added in.
	 */
	function sb_doing_it_wrong( $function, $message, $version ) {
		// @codingStandardsIgnoreStart
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();
		
		if ( wp_doing_ajax() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
		// @codingStandardsIgnoreEnd
	}
}

if( ! function_exists( 'sb_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/$template_name
	 * $default_path/$template_name
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 *
	 * @return string Template path.
	 */
	function sb_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = apply_filters( 'sb_template_path', 'smashballoon/');
		}

		if ( ! $default_path ) {
			$default_path = untrailingslashit( plugin_dir_path( __DIR__ ) . '/templates/' );
			$default_path = apply_filters( 'sb_default_template_path', $default_path );
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'sb_locate_template', $template, $template_name, $template_path );
	}
}

if ( ! function_exists( 'sb_get_template' ) ) {
	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name   Template name.
	 * @param array  $args            Arguments. (default: array).
	 * @param string $template_path   Template path. (default: '').
	 * @param string $default_path    Default path. (default: '').
	 */
	function sb_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path ) ) );
		$template  = (string) wp_cache_get( $cache_key, 'smashballoon' );

		if ( ! $template ) {
			$template = sb_locate_template( $template_name, $template_path, $default_path );
			wp_cache_set( $cache_key, $template, 'smashballoon' );
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$filter_template = apply_filters( 'sb_get_template', $template, $template_name, $args, $template_path, $default_path );

		if ( $filter_template !== $template ) {
			if ( ! file_exists( $filter_template ) ) {
				/* translators: %s template */
				sb_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'sb-notices' ), '<code>' . $template . '</code>' ), '6.2.2' );
				return;
			}
			$template = $filter_template;
		}

		$action_args = array(
			'template_name' => $template_name,
			'template_path' => $template_path,
			'located'       => $template,
			'args'          => $args,
		);

		if ( ! empty( $args ) && is_array( $args ) ) {
			if ( isset( $args['action_args'] ) ) {
				sb_doing_it_wrong(
					__FUNCTION__,
					__( 'action_args should not be overwritten when calling sb_get_template.', 'sb-notices' ),
					'1.0.0'
				);
				unset( $args['action_args'] );
			}
			extract( $args );
		}

		do_action( 'sb_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

		include $action_args['located'];

		do_action( 'sb_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
	}
}
