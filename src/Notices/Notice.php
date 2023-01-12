<?php
/**
 * Notice class
 *
 * @package SBNotices
 */

namespace Smashballoon\Notices\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Abstract Notice class.
 */
abstract class Notice {
	/**
	 * Notice type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Notice message
	 *
	 * @var string
	 */
	protected $message;

	/**
	 * Notice title
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Notice title class
	 *
	 * @var string
	 */
	protected $title_class;

	/**
	 * Notice title tag
	 *
	 * @var string
	 */
	protected $title_tag;

	/**
	 * Notice icon
	 *
	 * @var string
	 */
	protected $icon;

	/**
	 * Notice image
	 *
	 * @var string
	 */
	protected $image;

	/**
	 * Notice class
	 *
	 * @var string
	 */
	protected $class;

	/**
	 * Notice id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Notice wrap class
	 *
	 * @var string
	 */
	protected $wrap_class;

	/**
	 * Notice wrap id
	 *
	 * @var string
	 */
	protected $wrap_id;

	/**
	 * Notice data
	 *
	 * @var string
	 */
	protected $data;

	/**
	 * Notice dismissible
	 *
	 * @var bool
	 */
	protected $dismissible;

	/**
	 * Notice dismiss
	 *
	 * @var string
	 */
	protected $dismiss;

	/**
	 * Notice navigation
	 *
	 * @var bool
	 */
	protected $nav;

	/**
	 * Notice nav navigation
	 *
	 * @var string
	 */
	protected $navigation;

	/**
	 * Notice buttons
	 *
	 * @var array
	 */
	protected $buttons;

	/**
	 * Notice buttons wrap start
	 *
	 * @var string
	 */
	protected $buttons_wrap_start;

	/**
	 * Notice buttons wrap end
	 *
	 * @var string
	 */
	protected $buttons_wrap_end;

	/**
	 * Notice wrap schema
	 *
	 * @var string
	 */
	protected $wrap_schema;

	/**
	 * Notice styles
	 *
	 * @var string
	 */
	protected $styles;

	/**
	 * Notice fields
	 *
	 * @var array
	 */
	protected $fields = array(
		'wrap_class' => '',
		'wrap_id'    => '',
		'id'         => '',
		'class'      => '',
		'data'       => '',
		'icon'       => '',
		'image'      => '',
		'title'      => '',
		'message'    => '',
		'buttons'    => '',
		'dismiss'    => '',
		'navigation' => '',
		'styles'     => '',
	);

	/** Content allowed tags
	 *
	 * @var array
	 */
	protected $allowed_tags = array(
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'class'  => array(),
			'id'     => array(),
			'rel'    => array(),
		),
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'span'   => array(
			'class' => array(),
			'id'    => array(),
			'style' => array(),
		),
		'p'      => array(
			'class' => array(),
			'id'    => array(),
		),
		'div'    => array(
			'class' => array(),
			'id'    => array(),
		),
		'img'    => array(
			'src'   => array(),
			'class' => array(),
			'id'    => array(),
			'alt'   => array(),
		),
	);

	/**
	 * Current screen.
	 *
	 * @var string
	 */
	protected $screen;

	/**
	 * Notice constructor.
	 *
	 * @param $args
	 */
	public function __construct( $args ) {
		$this->screen = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$args         = wp_parse_args(
			$args,
			array(
				'type'               => 'error',
				'message'            => '',
				'title'              => '',
				'title_class'        => '',
				'title_tag'          => 'h3',
				'icon'               => '',
				'image'              => '',
				'class'              => '',
				'id'                 => '',
				'dismissible'        => false,
				'dismiss'            => '',
				'buttons'            => array(),
				'buttons_wrap_start' => '',
				'buttons_wrap_end'   => '',
				'wrap_schema'        => '<div {id} {class}>{icon}{title}{message}{buttons}</div>',
				'nav'                => false,
				'navigation'         => '',
				'wrap_class'         => '',
				'wrap_id'            => '',
				'data'               => '',
				'styles'             => '',
			)
		);

		$this->type               = $args['type'];
		$this->message            = $args['message'];
		$this->title              = $args['title'];
		$this->title_class        = $args['title_class'];
		$this->title_tag          = $args['title_tag'];
		$this->icon               = $args['icon'];
		$this->image              = $args['image'];
		$this->class              = $args['class'];
		$this->id                 = $args['id'];
		$this->wrap_class         = $args['wrap_class'];
		$this->wrap_id            = $args['wrap_id'];
		$this->data               = $args['data'];
		$this->dismissible        = $args['dismissible'];
		$this->dismiss            = $args['dismiss'];
		$this->buttons            = $args['buttons'];
		$this->buttons_wrap_start = $args['buttons_wrap_start'];
		$this->buttons_wrap_end   = $args['buttons_wrap_end'];
		$this->wrap_schema        = $args['wrap_schema'];
		$this->nav                = $args['nav'];
		$this->navigation         = $args['navigation'];
		$this->styles             = $args['styles'];
	}

	/**
	 * Display notice
	 *
	 * @return void
	 */
	abstract public function display();

	/**
	 * Replace fields in notice.
	 *
	 * @param string $notice
	 * @param array  $fields
	 *
	 * @return string
	 */
	public function replace_fields( $notice, $fields ) {
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $key => $value ) {
				$notice = str_replace( '{' . $key . '}', $value, $notice );
			}
		}

		return $notice;
	}

	/**
	 * Check condition.
	 *
	 * @param array $condition
	 *
	 * @return bool
	 */
	public function check_condition( $condition ) {
		$check = false;
		switch ( $condition['key'] ) {
			case 'screen':
				$check = $this->check_screen( $condition['compare'], $condition['value'] );
				break;
			case 'option':
				$check = $this->check_option( $condition['name'], $condition['compare'], $condition['value'] );
				break;
		}
		return $check;
	}


	/**
	 * Check screen.
	 *
	 * @param string $compare
	 * @param string $screen
	 *
	 * @return bool
	 */
	public function check_screen( $compare, $screen ) {
		$check = false;
		switch ( $compare ) {
			case '===':
				$check = $screen === $this->screen;
				break;
			case '!==':
				$check = $screen !== $this->screen;
				break;
		}
		return $check;
	}

	/**
	 * Check option.
	 *
	 * @param string $name
	 * @param string $compare
	 * @param string $value
	 *
	 * @return bool
	 */
	public function check_option( $name, $compare, $value ) {
		$check  = false;
		$option = get_option( $name );
		switch ( $compare ) {
			case '===':
				$check = $option === $value;
				break;
			case '!==':
				$check = $option !== $value;
				break;
		}
		return $check;
	}

}
