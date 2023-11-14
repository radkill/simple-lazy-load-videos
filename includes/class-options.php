<?php
/**
 * Class Options.
 *
 * @package simple-lazy-load-videos
 * @since 0.7.0
 */

namespace SLLV;

if ( ! class_exists( '\SLLV\Options' ) ) {
	/**
	 * Options page.
	 *
	 * @since 0.7.0
	 */
	class Options {

		/**
		 * Page slug.
		 *
		 * @since 0.7.0
		 *
		 * @var string $page_slug Options page slug.
		 */
		private $page_slug = 'simple-lazy-load-videos';


		/**
		 * Class initialization.
		 *
		 * @since 0.7.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'plugin_settings' ) );
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		}


		/**
		 * Add options page link to plugin actions.
		 *
		 * @since 0.7.0
		 *
		 * @param  string[] $actions     An array of plugin action links.
		 * @param  string   $plugin_file Path to the plugin file relative to the plugins directory.
		 * @return string                An array of plugin action links.
		 */
		public function plugin_action_links( $actions, $plugin_file ) {
			if ( false === strpos( $plugin_file, SLLV_PLUGIN_BASENAME ) ) {
				return $actions;
			}

			$settings_link = '<a href="options-general.php?page=' . $this->page_slug . '">' . __( 'Settings' ) . '</a>';
			array_unshift( $actions, $settings_link );
			return $actions;
		}


		/**
		 * Add options page.
		 *
		 * @since 0.7.0
		 */
		public function add_plugin_page() {
			add_options_page(
				__( 'Simple Lazy Load Videos', 'simple-lazy-load-videos' ),
				__( 'Simple Lazy Load Videos', 'simple-lazy-load-videos' ),
				'manage_options',
				$this->page_slug,
				array( $this, 'options_page_output' )
			);
		}


		/**
		 * Options page output.
		 *
		 * @since 0.7.0
		 */
		public function options_page_output() {
			?>

			<div class="wrap">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

				<form action="options.php" method="POST">
					<?php
						settings_fields( 'sllv_settings' );
						do_settings_sections( $this->page_slug );
						submit_button();
					?>
				</form>
			</div>

			<?php
		}


		/**
		 * Registering options.
		 *
		 * @since 0.7.0
		 */
		public function plugin_settings() {
			global $sllv;

			// Add settings.
			register_setting( 'sllv_settings', $sllv->get_settings_name(), array( $this, 'sanitize_callback' ) );

			// Add section.
			add_settings_section( 'sllv_section_settings', __( 'Settings' ), '', $this->page_slug );

			// Add fields.
			add_settings_field(
				'youtube_thumbnail_size',
				__( 'YouTube Thumbnail Size', 'simple-lazy-load-videos' ),
				array( $this, 'youtube_thumbnail_size' ),
				$this->page_slug,
				'sllv_section_settings'
			);

			add_settings_field(
				'vimeo_thumbnail_size',
				__( 'Vimeo Thumbnail Size', 'simple-lazy-load-videos' ),
				array( $this, 'vimeo_thumbnail_size' ),
				$this->page_slug,
				'sllv_section_settings'
			);
		}


		/**
		 * YouTube thumbnail size.
		 *
		 * @since 0.7.0
		 */
		public function youtube_thumbnail_size() {
			global $sllv;

			$name          = 'youtube_thumbnail_size';
			$values        = array(
				'hqdefault'     => 'hqdefault (480×360)',
				'sddefault'     => 'sddefault (640×480)',
				'maxresdefault' => 'maxresdefault (1280x720)',
			);
			$current_value = $sllv->get_settings( $name );
			?>

			<select name="<?php echo esc_attr( $sllv->get_settings_name() ); ?>[<?php echo esc_attr( $name ); ?>]">

				<?php foreach ( $values as $key => $value ) : ?>

					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_value, $key ); ?>><?php echo esc_html( $value ); ?></option>

				<?php endforeach; ?>

			</select>

			<?php
		}


		/**
		 * Vimeo thumbnail size.
		 *
		 * @since 0.7.1
		 */
		public function vimeo_thumbnail_size() {
			global $sllv;

			$name          = 'vimeo_thumbnail_size';
			$values        = array(
				'640'  => 'default (640×360)',
				'1280' => 'HD (1280×720)',
			);
			$current_value = $sllv->get_settings( $name );
			?>

			<select name="<?php echo esc_attr( $sllv->get_settings_name() ); ?>[<?php echo esc_attr( $name ); ?>]">

				<?php foreach ( $values as $key => $value ) : ?>

					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_value, $key ); ?>><?php echo esc_html( $value ); ?></option>

				<?php endforeach; ?>

			</select>

			<?php
		}


		/**
		 * Sanitize plugin settings.
		 *
		 * @since 0.7.0
		 *
		 * @param  array $options Plugin settings.
		 * @return array          Returned plugin settings.
		 */
		public function sanitize_callback( $options ) {
			global $sllv;

			if ( $options ) {
				foreach ( $options as $name => & $value ) {
					if ( in_array( $name, array( 'youtube_thumbnail_size', 'vimeo_thumbnail_size' ), true ) ) {
						$value = sanitize_text_field( $value );
					}
				}
			}

			return $options;
		}
	}
}
