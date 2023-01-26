<?php
/**
 * Notices class
 *
 * @package SBNotices
 */

namespace Smashballoon\Framework\Notices;

use Smashballoon\Framework\Notices\AdminNotice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all notices and display error warning or success notices
 *
 * @package Notice
 */
class SBNotices {

	/**
	 * Notices
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * Group notices
	 *
	 * @var array
	 */
	private $group_notices = array();

	/**
	 * Current screen.
	 *
	 * @var string
	 */
	private $screen;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->notices       = get_option( 'sbi_notices', array() );
		$this->screen        = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$this->group_notices = get_option( 'sbi_group_notices', array() );

		add_action( 'admin_notices', array( $this, 'display_notices' ) );
		add_action( 'sbi_admin_notices', array( $this, 'display_notices' ) );
	}

	/**
	 * Get all notices
	 */
	public function get_notices() {
		return $this->notices;
	}

	/**
	 * Set notices
	 *
	 * @param array $notices Notices.
	 */
	public function set_notices( $notices ) {
		$this->notices = $notices;
	}

	/**
	 * Get group notices
	 */
	public function get_group_notices() {
		return $this->group_notices;
	}

	/**
	 * Set group notices
	 *
	 * @param array $group_notices Group notices.
	 */
	public function set_group_notices( $group_notices ) {
		$this->group_notices = $group_notices;
	}

	/**
	 * Display notices
	 */
	public function display_notices() {
		// validate notifications.
		$this->validate_notices();

		if ( $this->notices ) {

			foreach ( $this->notices as $notice ) {
				// Check notice type.
				switch ( $notice['type'] ) {
					// Admin notice.
					case 'error':
					case 'warning':
					case 'information':
						$error = new AdminNotice( $notice );
						$error->display();
						break;

				}
			}
		}
	}

	/**
	 * Validate notices
	 */
	private function validate_notices() {
		$notices = $this->get_notices();

		if ( $notices ) {
			foreach ( $notices as $key => $notice ) {
				if ( ! isset( $notice['type'] ) || ! isset( $notice['message'] ) ) {
					unset( $notices[ $key ] );
				}
				// Check start and end date, and unset if expired.
				if ( $notice['start_date'] && $notice['end_date'] ) {
					if ( strtotime( $notice['start_date'] ) > time() || strtotime( $notice['end_date'] ) < time() ) {
						unset( $notices[ $key ] );
					}
				}
				// check page and unset if not match.
				if ( isset( $notice['page'] ) && ! empty( $notice['page'] ) ) {
					$page = $notice['page'];
					if ( ! is_array( $page ) ) {
						$page = array( $page );
					}
					if ( ! in_array( $this->screen, $page, true ) ) {
						unset( $notices[ $key ] );
					}
				}
				// if page has exclude then unset if match.
				if ( isset( $notice['page_exclude'] ) && ! empty( $notice['page_exclude'] ) ) {
					$page_exclude = $notice['page_exclude'];
					if ( ! is_array( $page_exclude ) ) {
						$page_exclude = array( $page_exclude );
					}
					if ( in_array( $this->screen, $page_exclude, true ) ) {
						unset( $notices[ $key ] );
					}
				}
				// check capability and unset if not match.
				if ( isset( $notice['capability'] ) && ! empty( $notice['capability'] ) ) {
					$capability = $notice['capability'];
					if ( ! is_array( $capability ) ) {
						$capability = array( $capability );
					}
					if ( ! current_user_can( $capability[0] ) ) {
						unset( $notices[ $key ] );
					}
				}
			}

			// notices are duplicate so unset them.
			$notices = array_unique( $notices, SORT_REGULAR );

			// sort notices as per priority value.
			uasort(
				$notices,
				function( $a, $b ) {
					if ( isset( $a['priority'] ) && isset( $b['priority'] ) ) {
						return $a['priority'] - $b['priority'];
					}
					return 255;
				}
			);

			// $notices = apply_filters( 'sbi_admin_notices', $notices );
			$this->set_notices( $notices );
		}
	}

	/**
	 * Get notice by id
	 *
	 * @param string $id
	 */
	public function get_notice( $id ) {
		$notices = $this->get_notices();
		return isset( $notices[ $id ] ) ? $notices[ $id ] : false;
	}

	/**
	 * Add notice
	 *
	 * @param string $id
	 * @param string $type
	 * @param array  $args
	 * @param string $group
	 */
	public function add_notice( $id, $type = 'error', $args, $group = false ) {
		if ( empty( $id ) || ( empty( $args['title'] ) && empty( $args['message'] ) ) ) {
			return;
		}

		$notices = $this->get_notices();

		// Check if notice already exists.
		if ( isset( $notices[ $id ] ) ) {
			return;
		}

		// Merge with defaults.
		$notice = wp_parse_args(
			$args,
			array(
				'id'          => $id,
				'type'        => $type,
				'message'     => '',
				'title'       => '',
				'icon'        => '',
				'class'       => '',
				'dismissible' => false,
				'priority'    => 255,
				'start_date'  => false,
				'end_date'    => false,
			)
		);

		// Add notice to notices array.
		$notices[ $id ] = $notice;

		if ( $group ) {
			// Add notice to group.
			$notices[ $id ]['group'] = $group;
		}

		// Update notices.
		$this->set_notices( $notices );
		update_option( 'sbi_notices', $notices );

		// Handle group notices.
		if ( $group ) {
			$group_notices = $this->get_group_notices();
			if ( ! isset( $group_notices[ $group ] ) ) {
				$group_notices[ $group ] = array();
			}
			$group_notices[ $group ][] = $id;
			$this->set_group_notices( $group_notices );
			update_option( 'sbi_group_notices', $group_notices );
		}
	}

	/**
	 * Remove notice by id
	 *
	 * @param string $id
	 */
	public function remove_notice( $id ) {
		$notices = $this->get_notices();

		if ( isset( $notices[ $id ] ) ) {
			// Handle group notices.
			$group_notices   = $this->get_group_notices();
			$is_group_notice = isset( $notices[ $id ]['group'] ) ? $notices[ $id ]['group'] : false;
			if ( $is_group_notice ) {
				$group_id = $notices[ $id ]['group'];
				if ( isset( $group_notices[ $group_id ] ) ) {
					$group_notices[ $group_id ] = array_diff( $group_notices[ $group_id ], array( $id ) );
					$this->set_group_notices( $group_notices );
					update_option( 'sbi_group_notices', $group_notices );
				}
			}

			unset( $notices[ $id ] );
			$this->set_notices( $notices );
			update_option( 'sbi_notices', $notices );
		}
	}
}
