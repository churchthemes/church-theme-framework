<?php
/**
 * Theme Customizer Controls
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// Make sure customizer is registered
if ( class_exists( 'WP_Customize_Control' ) ) {

	/**
	 * Textarea control class
	 *
	 * @since 0.9
	 */
	class CTFW_Customize_Textarea_Control extends WP_Customize_Control {

		public $type = 'textarea';

		public function render_content() {

			// allow description to be placed here
			do_action( 'ctfw_customize_textarea_control_before' );
			do_action( 'ctfw_customize_textarea_control_before-' . $this->id );

			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<textarea class="widefat" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
			</label>
			<?php

		}

	}

	/**
	 * Number control class
	 *
	 * @since 0.9
	 */
	class CTFW_Customize_Number_Control extends WP_Customize_Control {

		public $type = 'number';

		public function render_content() {

			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<input type="number" size="2" step="1" min="0" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
			</label>
			<?php

		}

	}

	/**
	 * Background image presets control class
	 *
	 * @since 1.4.1
	 */
	class CTFW_Customize_Background_Image_Presets_Control extends WP_Customize_Control {

		public $type = 'background_image_presets';

		public function render_content() {

			// Get presets
			$presets = ctfw_background_image_presets();
			$preset_urls = ctfw_background_image_preset_urls() ;

			// Set default if no value
			if ( empty( $this->value ) ) {

				// Is the URL stored in core background_image a preset?
				// It could be, because before WordPress 4.1 / Framework 1.4
				// the preset URL was always and only stored there
				// This makes for backward compatibility with separate preset field
				$background_image = get_background_image();
				if ( $background_image && in_array( $background_image, $preset_urls ) ) {
					$this->value = $background_image;
				}

				// Otherwise, first image preset is default
				else {
					$this->value = ctfw_background_image_first_preset_url();
				}

			}

			?>

			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />

			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			</label>

			<ul class="ctfw-customize-image-presets">

				<?php

				// Output presets thumbnails
				foreach ( $presets as $file => $data ) {

					$url = set_url_scheme( $data['url'] );
					$thumbnail_url = set_url_scheme( $data['thumb_url'] );

					$classes = array();

					// Selected?
					if ( $this->value == $url ) {
						$classes[] = 'ctfw-customize-image-preset-selected';
					}

					// Class(es)
					$class_attr = '';
					if ( $classes ) {
						$class_attr = 'class="' . esc_attr( implode( ' ', $classes ) ) . '" ';
					}

					?>

						<li href="#" <?php echo $class_attr; ?><?php if ( $data['colorable'] ) : ?>data-customize-image-title="<?php _e( 'Colorable', 'church-theme-framework' ); ?>"<?php endif; ?>
							data-customize-image-value="<?php echo esc_url( $url ); ?>"
							data-customize-image-preset-fullscreen="<?php echo esc_attr( $data['fullscreen'] ); ?>"
							data-customize-image-preset-repeat="<?php echo esc_attr( $data['repeat'] ); ?>"
							data-customize-image-preset-position="<?php echo esc_attr( $data['position'] ); ?>"
							data-customize-image-preset-attachment="<?php echo esc_attr( $data['attachment'] ); ?>"
							data-customize-image-preset-colorable="<?php echo esc_attr( $data['colorable'] ); ?>">
							<div class="ctfw-customize-thumbnail-wrapper"><img src="<?php echo esc_url( $thumbnail_url ); ?>" /></div>
						</li>

					<?php

				}

				?>

			</ul>

			<p class="ctfw-customize-background-image-preset-colorable" class="description" style="display: none">
				<?php _e( 'The selected image is <strong>colorable</strong>.', 'church-theme-framework' ); ?>
			</p>

			<?php

		}

	}

	/**
	 * Deprecated: Extended background image class
	 *
	 * IMPORTANT: This has no effect as of WordPress 4.1.
	 * The background image control was rewritten.
	 * Use CTFW_Customize_Background_Image_Presets_Control instead, as a separate control.
	 *
	 * This adds a Presets tab (multiple) in place of the Default tab (single)
	 *
	 * @since 0.9
	 */
	class CTFW_Customize_Background_Image_Control extends WP_Customize_Background_Image_Control {

		/**
		 * Constructor
		 *
		 * @since 0.9
		 */
		public function __construct( $manager, $id = false, $args = array() ) {

			parent::__construct( $manager );

			// Presets array
			$this->presets = isset( $args['presets'] ) ? $args['presets'] : array();

			// Add presets tab (multiple)
			if ( $this->presets ) {
				$this->add_tab( 'ctfw_presets',  _x( 'Presets', 'customizer', 'church-theme-framework' ),  array( $this, 'tab_preset_backgrounds' ) );
			}

			// Remove "Default" tab - user can select first in presets
			$this->remove_tab( 'default' );

		}

		/**
		 * List multiple presets
		 *
		 * @since 0.9
		 */
		public function tab_preset_backgrounds() {

			foreach ( $this->presets as $file => $data ) {

				// Output mimics print_tab_image() output but adds preset data attributes

				$url = set_url_scheme( $data['url'] );
				$thumbnail_url = set_url_scheme( $data['thumb_url'] );

				?>
				<a href="#" class="thumbnail" <?php if ( $data['colorable'] ) : ?>data-customize-image-title="<?php _e( 'Colorable', 'church-theme-framework' ); ?>"<?php endif; ?>
					data-customize-image-value="<?php echo esc_url( $url ); ?>"
					data-customize-image-preset-fullscreen="<?php echo esc_attr( $data['fullscreen'] ); ?>"
					data-customize-image-preset-repeat="<?php echo esc_attr( $data['repeat'] ); ?>"
					data-customize-image-preset-position="<?php echo esc_attr( $data['position'] ); ?>"
					data-customize-image-preset-attachment="<?php echo esc_attr( $data['attachment'] ); ?>"
					data-customize-image-preset-colorable="<?php echo esc_attr( $data['colorable'] ); ?>">
					<div class="ctfw-customizer-thumbnail-wrapper"><img src="<?php echo esc_url( $thumbnail_url ); ?>" /></div>
				</a>
				<?php

			}

		}

	}

}