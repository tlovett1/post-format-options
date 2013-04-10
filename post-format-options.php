<?php

/**
  * Plugin Name: Post Format Options
  * Plugin URI: http://wordpress.org/extend/plugins/post-format-options
  * Description: Allows you to disable post formats and manage which user rules can use which formats.
  * Author: Taylor Lovett
  * Version: 0.1
  * Author URI: http://taylorlovett.com
  */


class Post_Format_Options {

	// Constains singleton instance of Post_Format_Options object
	private static $_instance;
	private $option_name = 'post_format_options';
	private $option_defaults = array(
		'enabled' => 1
	);

	// Singleton class
	private function construct() { }

	/**
	 * Add plugin actions
	 *
	 * @uses add_action
	 * @since 0.1
	 * @return void
	 */
	public function add_actions() {
		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
	}

	/**
	 * Add options page
	 *
	 * @uses add_options_page, __
	 * @since 0.1
	 * @return void
	 */
	public function action_admin_menu() {
		add_options_page( __( 'Post Format Options', 'post-format-options' ), __( 'Post Format Options', 'post-format-options' ), 'manage_options', 'post-format-options.php', array( self::$_instance, 'screen_options' ) );
	}

	/**
	 * Add plugin filters
	 *
	 * @uses add_filter
	 * @since 0.1
	 * @return void
	 */
	public function add_filters() {
		add_filter( 'show_post_format_ui', array( $this, 'filter_show_post_format_ui' ), 10, 2 );
	}

	/**
	 * Enable/disable post format filter
	 *
	 * @param boolean $value
	 * @param object $post
	 * @uses get_option, wp_parse_args
	 * @since 0.1
	 * @return boolean
	 */
	public function filter_show_post_format_ui( $value, $post ) {
		$options = get_option( $this->option_name, $this->option_defaults );
        $options = wp_parse_args( $options, $this->option_defaults );

		return (bool) $options['enabled'];
	}

	/**
	 * Output settings for post formats
	 *
	 * @since 0.1
	 * @uses selected, get_option, wp_parse_args, settings_fields, _e
	 * @return void
	 */
	public function screen_options() {
		$options = get_option( $this->option_name, $this->option_defaults );
        $options = wp_parse_args( $options, $this->option_defaults );
    ?>
        <div class="wrap">
			<h2><?php _e( 'Post Format Options', 'post-format-options' ); ?></h2>
			
			<form action="options.php" method="post" enctype="multipart/form-data">
				<?php settings_fields( $this->option_name ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="pfo_enabled"><?php _e( 'Enable Post Formats:', 'post-format-options' ); ?></label></th>
							<td>
								<select id="pfo_enabled" name="<?php echo $this->option_name; ?>[enabled]">
									<option <?php selected( 1, $options['enabled'] ); ?> value="1"><?php _e( 'Enabled', 'post-format-options' ); ?></option>
									<option <?php selected( 0, $options['enabled'] ); ?> value="0"><?php _e( 'Disabled', 'post-format-options' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="submit" value="<?php _e( 'Update Settings', 'post-format-options' ); ?>" class="button-primary" />
				</p>
			</form>
		</div>
	<?php
	}

	/**
     * Sanitize options
     * 
     * @param array $options
     * @uses sanitize_text_field, get_option, wp_parse_args, sanitize_text_field
     * @return array
     */
	public function sanitize_options( $options ) {
		
		$current_options = get_option( $this->option_name, $this->option_defaults );
		$current_options = wp_parse_args( $current_options, $this->option_defaults );

		$new_options = array();

		foreach ( $this->option_defaults as $option_key => $option_default_value ) {
			$new_options[$option_key] = sanitize_text_field( $options[$option_key] );
		}

		return $new_options;
	}

	/**
	 * Initialize class and return an instance of it
	 *
	 * @since 0.1
	 * @return Post_Format_Options
	 */
	public function init() {
		if ( ! isset( self::$_instance ) ) {

			self::$_instance = new Post_Format_Options;
			self::$_instance->add_actions();
			self::$_instance->add_filters();
		}

		return self::$_instance;
	}

	/**
	 * Register setting and sanitization callback
	 * 
	 * @uses regsiter_setting
	 * @since 0.1
	 * @return void
	 */
	public function action_admin_init() {
		register_setting( $this->option_name, $this->option_name, array( $this, 'sanitize_options' ) );
	}

}

global $post_format_options;
$post_format_options = Post_Format_Options::init();

