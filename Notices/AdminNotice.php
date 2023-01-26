<?php
/**
 * Admin notice class.
 *
 * @package SBNotices
 * @subpackage Notices
 */

namespace Smashballoon\Framework\Notices;

use function Smashballoon\Framework\sb_get_template;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin notice class.
 */
class AdminNotice extends Notice {

	/**
	 * Display notice.
	 */
	public function display() {
		$class = 'notice-' . $this->type . ' ' . $this->class;
		if ( $this->dismissible ) {
			$class .= ' is-dismissible';
		}
		$id         = $this->id;
		$wrap_class = $this->wrap_class;
		$wrap_id    = $this->wrap_id;

		$data   = $this->data;
		$styles = $this->styles;

		$title       = $this->title;
		$title_class = $this->title_class;
		$title_tag   = $this->title_tag;

		$icon  = $this->icon;
		$image = $this->image;

		$message = $this->message;

		$buttons      = $this->buttons;
		$wrap_schema  = $this->wrap_schema;
		$fields       = $this->fields;
		$allowed_tags = $this->allowed_tags;
		$dismiss      = $this->dismiss;
		$navigation   = $this->navigation;

		// Display notice.
		foreach ( $fields as $field => $value ) :
			switch ( $field ) :
				case 'id':
					$fields['id'] = $id ? 'id="' . esc_attr( $id ) . '"' : '';
					break;

				case 'class':
					$fields['class'] = $class ? 'class="' . esc_attr( $class ) . '"' : '';
					break;

				case 'wrap_id':
					$fields['wrap_id'] = $wrap_id ? 'id="' . esc_attr( $wrap_id ) . '"' : '';
					break;

				case 'wrap_class':
					$fields['wrap_class'] = $wrap_class ? 'class="' . esc_attr( $wrap_class ) . '"' : '';
					break;

				case 'styles':
					$styles_attr = '';
					if ( isset( $styles ) && is_array( $styles ) ) {
						foreach ( $styles as $styles_key => $styles_value ) {
							if ( is_array( $styles_value ) && isset( $styles_value['condition'] ) ) {
								$check        = $this->check_condition( $styles_value['condition'] );
								$styles_value = $check ? $styles_value['true'] : $styles_value['false'];
							}
							$styles_attr .= esc_attr( $styles_key ) . ':' . esc_attr( $styles_value ) . ';';
						}
					} else {
						$styles_attr = $styles;
					}
					$fields['styles'] = $styles_attr ? 'style="' . $styles_attr . '"' : '';
					break;

				case 'data':
					$data_attr = '';
					if ( isset( $data ) && is_array( $data ) ) {
						foreach ( $data as $data_key => $data_value ) {
							$data_attr .= 'data-' . esc_attr( $data_key ) . '="' . esc_attr( $data_value ) . '" ';
						}
					}
					$fields['data'] = $data_attr;
					break;

				case 'image':
				case 'icon':
					if ( $image || $icon ) {
						$image = $image ? $image : $icon;
						// Check if image is a url.
						if ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
							$image_html = '<img src="' . esc_url( $image ) . '" />';
						} elseif ( is_array( $image ) ) {
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
						} else {
							$image_html = $image;
						}
					}

					$fields['image'] = $image_html;
					$fields['icon']  = $image_html;
					break;

				case 'title':
					$fields['title'] = $title ? '<' . esc_attr( $title_tag ) . ' class="' . esc_attr( $title_class ) . '">' . esc_html( $title ) . '</' . esc_attr( $title_tag ) . '>' : '';
					break;

				case 'message':
					$fields['message'] = $message ? wp_kses( $message, $allowed_tags ) : '';
					break;

				case 'buttons':
					$buttons_html = '';
					if ( ! empty( $buttons ) ) {
						$buttons_html .= isset( $this->buttons_wrap_start ) ? wp_kses( $this->buttons_wrap_start, $allowed_tags ) : '';
						foreach ( $buttons as $button ) {
							// conditional check for button.
							if ( isset( $button['condition'] ) ) {
								if ( ! $this->check_condition( $button['condition'] ) ) {
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

							$data_attr = '';
							if ( isset( $button['data'] ) && is_array( $button['data'] ) ) {
								foreach ( $button['data'] as $data_key => $data_value ) {
									$data_attr .= 'data-' . esc_attr( $data_key ) . '="' . esc_attr( $data_value ) . '" ';
								}
							}

							$buttons_html .= '<' . esc_attr( $tag ) . ' ' . $btn_href . ' ' . $btn_class . ' ' . $btn_id . ' ' . $btn_target . ' ' . $btn_rel . ' ' . $btn_vue . ' ' . $data_attr . '>' . $btn_text . '</' . esc_attr( $tag ) . '>';

						}
						$buttons_html .= isset( $this->buttons_wrap_end ) ? wp_kses( $this->buttons_wrap_end, $allowed_tags ) : '';
					}
					$fields['buttons'] = $buttons_html;
					break;

				case 'dismiss':
					$dismiss_html = '';
					if ( $this->dismissible ) {
						if ( is_array( $dismiss ) ) {
							$icon         = isset( $dismiss['icon'] ) ? esc_url( $dismiss['icon'] ) : '';
							$image_html   = '<img src="' . esc_url( $icon ) . '" />';
							$title        = isset( $dismiss['title'] ) ? esc_attr( $dismiss['title'] ) : esc_attr__( 'Dismiss this message', 'instagram-feed' );
							$class        = isset( $dismiss['class'] ) ? esc_attr( $dismiss['class'] ) : '';
							$tag          = isset( $dismiss['tag'] ) ? esc_attr( $dismiss['tag'] ) : 'a';
							$attr         = isset( $dismiss['attr'] ) ? $dismiss['attr'] : '';
							$dismiss_html = '<' . $tag . ' class="' . $class . '" title="' . $title . '" ' . $attr . '>' . $image_html . '</' . $tag . '>';

						} elseif ( is_string( $dismiss ) ) {
							$dismiss_html = $dismiss;
						}
					}
					$fields['dismiss'] = wp_kses( $dismiss_html, $allowed_tags );
					break;

				case 'navigation':
					$navigation_html = '';
					if ( $this->nav ) {
						if ( is_array( $navigation ) ) {
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
						} elseif ( is_string( $navigation ) ) {
							$navigation_html = $navigation;
						}
					}
					$fields['navigation'] = $navigation_html;
					break;

			endswitch;
		endforeach;

		// Replace fields in wrap schema.
		$notice = $this->replace_fields( $wrap_schema, $fields );
		$this->print_notice( $notice );
	}

	public function print_notice( $notice ) {
		ob_start();

		sb_get_template(
			"notices/{$this->type}.php",
			array(
				'notice' => $notice,
				'type'   => $this->type,
				'id'     => $this->id,
			)
		);

		$notice_html = ob_get_clean();
		$notice_html = apply_filters( "sb_{$this->type}_notice_markup", $notice_html );
		echo $notice_html;
	}

}
