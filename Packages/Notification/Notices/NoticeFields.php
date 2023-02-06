<?php
/**
 * Notice fields class
 * 
 * @package Notices
 */

namespace Smashballoon\Framework\Packages\Notification\Notices;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Notice fields class.
 */
class NoticeFields {

    /**
	 * Current screen.
	 *
	 * @var string
	 */
	protected static $screen;

	/**
	 * Set current screen.
	 * 
	 * @param string $screen Current screen.
	 */
	public static function set_screen( $screen ) {
		self::$screen = $screen;
	}

    /** Content allowed tags
	 *
	 * @var array
	 */
	protected static $allowed_tags = array(
		'a'      => array(
			'href'   => array(),
			'title'  => array(),
			'target' => array(),
			'class'  => array(),
			'id'     => array(),
			'rel'    => array(),
			'style'  => array(),
			'data-*'  => true,
		),
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'span'   => array(
			'class' => array(),
			'id'    => array(),
			'style' => array(),
			'data-*'  => true,
		),
		'p'      => array(
			'class' => array(),
			'id'    => array(),
			'style' => array(),
			'data-*'  => true,
		),
		'div'    => array(
			'class' => array(),
			'id'    => array(),
			'style' => array(),
			'data-*'  => true,
		),
		'img'    => array(
			'src'   => array(),
			'class' => array(),
			'id'    => array(),
			'alt'   => array(),
		),
		'button' => array(
			'class' => array(),
			'id'    => array(),
			'type'  => array(),
			'style' => array(),
			'data-*'  => true,
		),
	);

    /**
	 * Get id attribute for notice.
	 * @param string $id
	 * @return string
	 */
	public static function get_id( $id ) {
		return $id ? 'id="' . esc_attr( $id ) . '"' : '';
	}

	/**
	 * Get class attribute for notice.
	 * @param string $class
	 * @return string
	 */
	public static function get_class( $class ) {
		return $class ? 'class="' . esc_attr( $class ) . '"' : '';
	}

	/**
	 * Get style attribute for notice.
	 * @param mixed $styles
	 * @return string
	 */
	public static function get_styles( $styles ) {
		$styles_attr = '';
		if ( isset( $styles ) && is_array( $styles ) ) {
			foreach ( $styles as $styles_key => $styles_value ) {
				if ( is_array( $styles_value ) && isset( $styles_value['condition'] ) ) {
					$check        = self::check_condition( $styles_value['condition'] );
					$styles_value = $check ? $styles_value['true'] : $styles_value['false'];
				}
				$styles_attr .= esc_attr( $styles_key ) . ':' . esc_attr( $styles_value ) . ';';
			}
		} elseif ( isset( $styles ) && is_string( $styles ) ) {
			$styles_attr = $styles;
		}
		return $styles_attr ? 'style="' . $styles_attr . '"' : '';
	}

	/**
	 * Get data attribute for notice.
	 * @param mixed $data
	 * @return string
	 */
	public static function get_data( $data ) {
		$data_attr = '';
		if ( isset( $data ) && is_array( $data ) ) {
			foreach ( $data as $data_key => $data_value ) {
				if ( is_array( $data_value ) && isset( $data_value['condition'] ) ) {
					$check       = self::check_condition( $data_value['condition'] );
					$data_value  = $check ? $data_value['true'] : $data_value['false'];
				}
				$data_attr .= 'data-' . esc_attr( $data_key ) . '="' . esc_attr( $data_value ) . '" ';
			}
		}
		return $data_attr;
	}

	/**
	 * Get image/icon for notice.
	 * @param mixed $image
	 * @return string
	 */
	public static function get_image( $image ) {
		// Check if image is a url.
		if ( isset( $image ) && filter_var( $image, FILTER_VALIDATE_URL ) ) {
			$image_html = '<img src="' . esc_url( $image ) . '" />';
		}
		
		if ( isset( $image ) && is_array( $image ) ) {
			$src          = isset( $image['src'] ) ? 'src="' . esc_url( $image['src'] ) . '"' : '';
			$alt          = isset( $image['alt'] ) ? ' alt="' . esc_attr( $image['alt'] ) . '"' : '';
			$wrap         = isset( $image['wrap'] ) ? $image['wrap'] : '';
			$overlay      = isset( $image['overlay'] ) ? $image['overlay'] : '';
			$overlay_wrap = isset( $image['overlay_wrap'] ) ? esc_html( $image['overlay_wrap'] ) : '';
			$overlay_wrap = str_replace( '{overlay}', $overlay, $overlay_wrap );

			if ( $wrap ) {
				$wrap       = str_replace( '{src}', $src, $wrap );
				$wrap       = str_replace( '{alt}', $alt, $wrap );
				$wrap       = str_replace( '{overlay}', $overlay_wrap, $wrap );
				$image_html = $wrap;
			} else {
				$image_html = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" />';
			}

		} elseif ( isset( $image ) && is_string( $image ) ) {
			$image_html = $image;
		}
		return $image_html;
	}

	/**
	 * Get title for notice.
	 * @param mixed $title
	 * @return string
	 */
	public static function get_title( $title ) {
		$title_html = '';
		if( isset( $title ) && is_array( $title ) ) {
			$title_tag   = isset( $title['tag'] ) ? $title['tag'] : 'h3';
			$title_class = isset( $title['class'] ) ? $title['class'] : '';
			$title 	     = isset( $title['text'] ) ? $title['text'] : '';

			$title_html = $title ? '<' . esc_attr( $title_tag ) . ' class="' . esc_attr( $title_class ) . '">' . esc_html( $title ) . '</' . esc_attr( $title_tag ) . '>' : '';

		} elseif ( isset( $title ) && is_string( $title ) ) {
			$title_html = '<h3>' . esc_html( $title ) . '</h3>';
		}
		return $title_html;
	}

	/**
	 * Get content for notice.
	 * @param mixed $content
	 * @return string
	 */
	public static function get_content( $content ) {
		$content_html = '';
		if ( isset( $content ) && is_array( $content ) ) {
			$content_tag   = isset( $content['tag'] ) ? $content['tag'] : 'p';
			$content_class = isset( $content['class'] ) ? $content['class'] : '';
			$content 	   = isset( $content['text'] ) ? $content['text'] : '';

			$content_html = $content ? '<' . esc_attr( $content_tag ) . ' class="' . esc_attr( $content_class ) . '">' . esc_html( $content ) . '</' . esc_attr( $content_tag ) . '>' : '';

		} elseif ( isset( $content ) && is_string( $content ) ) {
			$content_html = wp_kses( $content, self::$allowed_tags );
		}
		return $content_html;
	}

	/**
	 * Get button for notice.
	 * @param mixed $buttons
	 * @param string $wrap_start
	 * @param string $wrap_end
	 * 
	 * @return string
	 */
	public static function get_buttons( $buttons, $wrap_start, $wrap_end ) {
		$buttons_html = '';
		if ( isset( $buttons ) && is_array( $buttons ) ) {
			$buttons_html .= isset( $wrap_start ) ? wp_kses( $wrap_start, self::$allowed_tags ) : '';
			
			foreach ( $buttons as $button ) {
				// conditional check for button.
				if ( isset( $button['condition'] ) ) {
					if ( ! self::check_condition( $button['condition'] ) ) {
						continue;
					}
				}

				$tag        = isset( $button['tag'] ) ? $button['tag'] : 'a';
				$btn_class  = isset( $button['class'] ) ? 'class="' . esc_attr( $button['class'] ) . '"' : '';
				$btn_id     = isset( $button['id'] ) ? 'id="' . esc_attr( $button['id'] ) . '"' : '';
				$btn_target = isset( $button['target'] ) ? 'target="' . esc_attr( $button['target'] ) . '"' : '';
				$btn_rel    = isset( $button['rel'] ) ? 'rel="' . esc_attr( $button['rel'] ) . '"' : '';
				$btn_href   = isset( $button['url'] ) ? 'href="' . esc_url( $button['url'] ) . '"' : '';
				$btn_vue    = isset( $button['vue'] ) ? $button['vue'] : '';
				$btn_text   = isset( $button['text'] ) ? esc_html( $button['text'] ) : '';
				$btn_data   = isset( $button['data'] ) ? self::get_data( $button['data'] ) : '';

				$buttons_html .= '<' . esc_attr( $tag ) . ' ' . $btn_class . ' ' . $btn_id . ' ' . $btn_target . ' ' . $btn_rel . ' ' . $btn_href . ' ' . $btn_vue . ' ' . $btn_data . '>' . $btn_text . '</' . esc_attr( $tag ) . '>';
			}
			$buttons_html .= isset( $wrap_end ) ? wp_kses( $wrap_end, self::$allowed_tags ) : '';
		}
		return $buttons_html;
	}

	/**
	 * Get dismiss button for notice.
	 * @param mixed $dismiss
	 * @return string
	 */
	public static function get_dismiss( $dismiss ) {
		$dismiss_html = '';
		if ( isset( $dismiss ) && is_array( $dismiss ) ) {
			$icon         = isset( $dismiss['icon'] ) ? esc_url( $dismiss['icon'] ) : '';
			$image_html   = '<img src="' . esc_url( $icon ) . '" />';
			$title        = isset( $dismiss['title'] ) ? esc_attr( $dismiss['title'] ) : esc_attr__( 'Dismiss this message', 'instagram-feed' );
			$class        = isset( $dismiss['class'] ) ? esc_attr( $dismiss['class'] ) : '';
			$tag          = isset( $dismiss['tag'] ) ? esc_attr( $dismiss['tag'] ) : 'a';
			$attr         = isset( $dismiss['attr'] ) ? $dismiss['attr'] : '';

			$dismiss_html = '<' . $tag . ' class="' . $class . '" title="' . $title . '" ' . $attr . '>' . $image_html . '</' . $tag . '>';

		} elseif ( isset( $dismiss ) && is_string( $dismiss ) ) {
			$dismiss_html = $dismiss;
		}

		$dismiss_html = wp_kses( $dismiss_html, self::$allowed_tags );
		return $dismiss_html;
	}

	/**
	 * Get navigation for notice.
	 * @param mixed $navigation
	 * @return string
	 */
	public static function get_navigation( $navigation ) {
		$navigation_html = '';
		if ( isset( $navigation ) && is_array( $navigation ) ) {
			$class     = isset( $navigation['class'] ) ? esc_attr( $navigation['class'] ) : '';
			$tag       = isset( $navigation['tag'] ) ? esc_attr( $navigation['tag'] ) : 'a';
			$item_html = '';
			foreach ( $navigation['items'] as $item ) {
				$item_class = isset( $item['class'] ) ? esc_attr( $item['class'] ) : '';
				$item_tag   = isset( $item['tag'] ) ? esc_attr( $item['tag'] ) : 'a';
				$icon       = isset( $item['icon'] ) ? esc_url( $item['icon'] ) : '';
				$image_html = '<img src="' . esc_url( $icon ) . '" />';
				$title      = isset( $item['title'] ) ? esc_attr( $item['title'] ) : '';
				$attr       = isset( $item['attr'] ) ? esc_attr( $item['attr'] ) : '';
				
				$item_html .= '<' . $item_tag . ' class="' . $item_class . '" title="' . $title . '" ' . $attr . '>' . $image_html . '</' . $item_tag . '>';
			}
			$navigation_html = '<' . $tag . ' class="' . $class . '">' . $item_html . '</' . $tag . '>';

		} elseif ( isset( $navigation ) && is_string( $navigation ) ) {
			$navigation_html = $navigation;
		}

		$navigation_html = wp_kses( $navigation_html, self::$allowed_tags );
		return $navigation_html;
	}

    /**
	 * Check condition.
	 *
	 * @param array $condition
	 *
	 * @return bool
	 */
	public static function check_condition( $condition ) {
		$check = false;
		switch ( $condition['key'] ) {
			case 'screen':
				$check = self::check_screen( $condition['compare'], $condition['value'] );
				break;
			case 'option':
				$check = self::check_option( $condition['name'], $condition['compare'], $condition['value'] );
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
	public static function check_screen( $compare, $screen ) {
		$check = false;
		switch ( $compare ) {
			case '===':
				$check = $screen === self::$screen;
				break;
			case '!==':
				$check = $screen !== self::$screen;
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
	public static function check_option( $name, $compare, $value ) {
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
