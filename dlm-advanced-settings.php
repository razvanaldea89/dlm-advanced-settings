<?php
/*
	Plugin Name: Download Monitor - Advanced Settings
	Plugin URI: https://github.com/razvanaldea89/dlm-advanced-settings
	Description: A lightweight plugin that taps into Download Monitor's hooks and offers a way to manipulate them via the admin panel.
	Version: 1.0.0
	Author: raldea89
	Author URI: https://github.com/razvanaldea89/
	License: GPL v3
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main class of the plugin
 *
 * @since 1.0.0
 */
class DLM_Advanced_Settings {

	/**
	 * Plugin version
	 *
	 * @since 1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * Plugin instance
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public static $instance = null;

	/**
	 * Variable that will contain Download Monitor's hooks
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $hooks = array();

	/**
	 * Variable that will contain PHP info
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $php_info = array();

	/**
	 * Variable that will contain the settings
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $settings = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Check if Download Monitor is installed and activated.
		if ( ! class_exists( 'WP_DLM' ) ) {
			add_action( 'admin_notices', array( $this, 'dlm_needed_notice' ) );

			return;
		}

		$memory_limit = ini_get( 'memory_limit' );
		if ( preg_match( '/^(\d+)(.)$/', $memory_limit, $matches ) ) {
			if ( 'M' === $matches[2] ) {
				$memory_limit = $matches[1];
			} elseif ( 'K' === $matches[2] ) {
				$memory_limit = $matches[1] / 1024;
			} elseif ( 'G' === $matches[2] ) {
				$memory_limit = $matches[1] * 1024;
			}
		}

		$this->php_info = array(
			'memory_limit'          => absint( $memory_limit ),
			'max_execution_time'    => ini_get( 'max_execution_time' ),
			'retrieved_rows'        => 10000,
			'retrieved_user_data'   => 5000,
			'retrieved_chart_stats' => 1000,
		);

		if ( 40 < $this->php_info['memory_limit'] ) {
			if ( 80 <= $this->php_info['memory_limit'] ) {
				$this->php_info['retrieved_rows'] = 30000;
			}

			if ( 120 <= $this->php_info['memory_limit'] ) {
				$this->php_info['retrieved_rows'] = 40000;
			}
			if ( 150 <= $this->php_info['memory_limit'] ) {
				$this->php_info['retrieved_rows'] = 60000;
			}

			if ( 200 <= $this->php_info['memory_limit'] ) {
				$this->php_info['retrieved_rows'] = 100000;
			}

			if ( 500 <= $this->php_info['memory_limit'] ) {
				$this->php_info['retrieved_rows'] = 150000;
			}
		}

		$this->hooks = array(
			'dlm_delete_files'          => array(
				'label'   => __( 'Delete files when deleting a download', 'dlm-advanced-settings' ),
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'Enabling this will let you automatically delete files associated with a Download upon the Download deletion', 'dlm-advanced-settings' )
			),
			'dlm_hotlink_protection'    => array(
				'label'   => __( 'Hotlink protection','dlm-advanced-settings' ),
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'Enabling this will allow the download handler to check the PHP referer to see if it originated from your site and if not, redirect them to the homepage.', 'dlm-advanced-settings' )
			),
			'dlm_allow_x_forwarded_for' => array(
				'label'   => __( 'Allow Proxy IP Override', 'dlm-advanced-settings' ),
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'If enabled, Download Monitor will use the <code>X_FORWARDED_FOR</code> HTTP header set by proxies as the IP address. Note that anyone can set this header, making it less secure.', 'dlm-advanced-settings' )
			),
			'dlm_x_sendfile'            => array(
				'label'   => 'Enable X-Accel-Redirect / X-Sendfile',
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'If supported, <code>X-Accel-Redirect / X-Sendfile</code> can be used to serve downloads instead of PHP (server requires mod_xsendfile) Attention! Enabling this option will disable the XHR functionality!', 'dlm-advanced-settings' )
			),
			'dlm_timestamp_link'        => array(
				'label'   => __( 'Show a timestamp in the download link', 'dlm-advanced-settings' ),
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'If enabled, the download URL will have a timestamp attached as a parameter. This serves as a cache preventing solution for plugins that cache the URL.', 'dlm-advanced-settings' )
			),
			'dlm_enable_reports'        => array(
				'label'   => __( 'Enable reports', 'dlm-advanced-settings' ),
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'Enable or disable the Reports functionality. This will also enable/disable logging detailed info into the Logs Table of the Database. Disabling this will not disable the download count funtionality.', 'dlm-advanced-settings' )
			),
			'dlm_hide_meta_version'     => array(
				'label'   => __( 'Hide meta version in header', 'dlm-advanced-settings' ),
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'Hide or show Download Monitor\'s version in the HTML\'s head.', 'dlm-advanced-settings' )
			),
			'dlm_count_meta_downloads'  => array(
				'label'   => __( 'Add meta value to download count', 'dlm-advanced-settings' ),
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'Add the manual download count ( the one set when editing a Download ) to the total download count of a Download', 'dlm-advanced-settings' )
			),
			'dlm_do_xhr'                => array(
				'label'   => __( 'XHR downloads', 'dlm-advanced-settings' ),
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
				'description' => __( 'Enable or disable downloading files using the newly XHR functionality. Disabling this may cause the reports to not be so pricise.', 'dlm-advanced-settings' )
			),
			'dlm_restricted_file_types' => array(
				'label'   => __( 'Restricted file types', 'dlm-advanced-settings' ),
				'default' => '',
				'params'  => 2,
				'type'    => 'text',
				'description' => __( 'Define extra file types that should be restricted. Each file type needs to be separated by a comma (<code>,</code>). Only input the extension( ex.: txt, pdf ).', 'dlm-advanced-settings' )
			),
			'dlm_404_redirect'          => array(
				'label'   => __( '404 redirect URL', 'dlm-advanced-settings' ),
				'default' => '',
				'params'  => 1,
				'type'    => 'text',
				'description' => __( 'Define a custom 404 redirect for when a Download can\'t be found.', 'dlm-advanced-settings' )
			),
			'dlm_placeholder_image_src' => array(
				'label'   => __( 'Placeholder image src', 'dlm-advanced-settings' ),
				'default' => download_monitor()->get_plugin_url() . '/assets/images/placeholder.png',
				'params'  => 1,
				'type'    => 'text',
				'description' => __( 'Define a custom URL for the Download CPT placeholder.', 'dlm-advanced-settings' )
			),
			'dlm_reports_server_limits' => array(
				'label'   => __( 'Reports server limits', 'dlm-advanced-access' ),
				'default' => $this->php_info,
				'params'  => 1,
				'type'    => 'multi_text',
				'description' => __( 'Define other servet limits. Usefull when you have any problems with the Reports not being displayed a possible problem might be your server\'s lack of resources. This way you can control how much data is retrieved in one request.', 'dlm-advanced-settings' )
			),
			'dlm_xhr_progress'          => array(
				'label'   => __( 'XHR progress animation', 'dlm-advanced-settings' ),
				'default' => array(
					'display'   => true,
					'animation' => includes_url( '/images/spinner.gif' ),
				),
				'params'  => 1,
				'type'    => 'multi_text',
				'description' => __( 'Define whether to display the XHR progress or not. Also, define a custom URL for the loading animation.', 'dlm-advanced-settings' )
			),
		);

		$defaults = array();
		foreach ( $this->hooks as $key => $setting ) {
			$defaults[ $key ] = $setting['default'];
		}

		$this->settings = wp_parse_args( get_option( 'dlm-as-settings', array() ), $defaults );

		$this->set_wp_hooks();
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_Advanced_Settings object.
	 * @since 1.0.0
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Advanced_Settings ) ) {
			self::$instance = new DLM_Advanced_Settings();
		}

		return self::$instance;
	}

	/**
	 * Set plugin hooks.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function set_wp_hooks(){
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'dlm_admin_menu_links', array( $this, 'add_submenu_page' ), 120 );
		add_action( 'init', array( $this, 'set_dlm_hooks' ) );
		add_action( 'pre_update_option', array( $this, 'sanitize_settings' ), 15, 3 );
	}

	/**
	 * Register settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		$group = 'dlm-as-settings';
		register_setting( $group, $group );
		foreach ( $this->hooks as $key => $setting ) {
			add_settings_field(
				$key,
				$setting['label'],
				'__return_false',
				$group,
				$group,
				array( 'key' => $key )
			);
		}
	}

	/**
	 * Add the submenu page to the Downloads menu
	 *
	 * @param array $links The links array.
	 *
	 * @since 1.0.0
	 */
	public function add_submenu_page( $links ) {
		$links[] = array(
			'page_title' => __( 'Advanced Settings', 'dlm-advanced-settings' ),
			'menu_title' => __( 'Advanced Settings', 'dlm-advanced-settings' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'dlm-advanced-settings',
			'function'   => array( $this, 'render_submenu_page' ),
			'priority'   => 19,
		);

		return $links;
	}

	/**
	 * Render the submenu page
	 *
	 * @since 1.0.0
	 */
	public function render_submenu_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Advanced Settings', 'dlm-advanced-settings' ); ?></h2>
			<p><?php echo wp_kses_post( __( 'These are considered advanced setting. Each of it hooks onto a filter from Download Monitor and manipulates the result. These are used so that users won\'t have to add custom code to their theme\'s <code>functions.php</code> file.' , 'dlm-advanced-settings' ) ); ?></p>
			<form method="post"
					action="options.php">
				<?php
				// Set our registered options.
				settings_fields( 'dlm-as-settings' );

				$html = '<table class="form-table"><tbody>';
				// Cycle through settings.
				foreach ( $this->hooks as $hook => $option ) {
					$html .= '<tr>';
					switch ( $option['type'] ) {
						case 'checkbox':
							$html .= '<th scope="row"><label>' . esc_html( $option['label'] ) . '</label></th>';
							$html .= '<td><div class="wpchill-toggle">
									<input class="wpchill-toggle__input" type="checkbox" name="dlm-as-settings[' . esc_attr( $hook ) . ']" value="1" ' . checked( $this->settings[ $hook ], '1', false ) . ' />
									<div class="wpchill-toggle__items">
										<span class="wpchill-toggle__track"></span>
										<span class="wpchill-toggle__thumb"></span>
										<svg class="wpchill-toggle__off" width="6" height="6" aria-hidden="true" role="img"
										     focusable="false" viewBox="0 0 6 6">
											<path d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path>
										</svg>
										<svg class="wpchill-toggle__on" width="2" height="6" aria-hidden="true" role="img"
										     focusable="false" viewBox="0 0 2 6">
											<path d="M0 0h2v6H0z"></path>
										</svg>
									</div>
								</div>
								<p>' . wp_kses_post( $option['description'] ) . '</p></td>';
							break;
						case 'text':
							$html .= '<th scope="row"><label for="dlm-as-settings[' . esc_attr( $hook ) . ']">' . esc_html( $option['label'] ) . '</label></th>';
							$html .= '<td><input type="text" name="dlm-as-settings[' . esc_attr( $hook ) . ']" value="' . esc_attr( $this->settings[ $hook ] ) . '" placeholder="' . esc_attr( $option['default'] ) . '" /><p>' . wp_kses_post( $option['description'] ) . '</p></td>';
							break;
						case 'multi_text':
							$html .= '<th scope="row">' . esc_html( $option['label'] ) . '</th>';
							$html .= '<td>';
							foreach ( $option['default'] as $key => $value ) {
								$html .= '<p>';
								$html .= '<input type="text" name="dlm-as-settings[' . esc_attr( $hook ) . ' ][' . esc_attr( $key ) . ']" value="' . esc_attr( $this->settings[ $hook ][ $key ] ) . '" placeholder="' . esc_attr( $value ) . '" />';
								$html .= '<label for="dlm-as-settings[' . esc_attr( $hook ) . ' ][' . esc_attr( $key ) . ']">' . esc_html( $key ) . '</label>';
								$html .= '</p>';
							}
							$html .= '<p>' . wp_kses_post( $option['description'] ) . '</p></td>';
							break;
					}
					$html .= '</tr>';
				}
				$html .= '<tr><th scope="row">' . get_submit_button( 'Save Settings' ) . '</th><td></td></tr>';
				$html .= '</tbody></table>';
				echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Add a notice that Download Monitor is needed for this plugin to work
	 *
	 * @since 1.0.0
	 */
	public function dlm_needed() {
		// Add our WP Notice.
		?>
		<div class="error">
			<p>
				<strong>
					<?php
					esc_html_e(
						'Download Monitor - Advanced Settings requires Download Monitor to
					be installed and activated.',
						'dlm-advanced-settings'
					);
					?>
			</p>
		</div>
		<?php
	}

	/**
	 * Set our hooks
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function set_dlm_hooks() {

		// Check if we have settings.
		if ( empty( $this->settings ) ) {
			return;
		}
		require_once __DIR__ . '/class-dlm-as-hooks.php';
		$hooks_class = DLM_AS_Hooks::get_instance( $this->settings );
		// Cycle through settings.
		foreach ( $this->settings as $key => $value ) {
			if ( ! isset( $this->hooks[ $key ] ) ) {
				continue;
			}
			// Set the required hook.
			add_filter(
				$key,
				array( $hooks_class, 'filter_' . $key ),
				15,
				$this->hooks[ $key ]['params']
			);
		}
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $value The new value.
	 * @param array $option     The option.
	 * @param array $old_value    The old value.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function sanitize_settings( $value, $option, $old_value ) {
		// If we're not dealing with our option, return the value.
		if ( 'dlm-as-settings' !== $option ) {
			return $value;
		}
		// Add '1' or '0' only to the checkbox values.
		foreach ( $this->hooks as $key => $setting ) {
			if ( 'checkbox' !== $setting['type'] ) {
				continue;
			}

			if ( ! isset( $value[ $key ] ) ) {
				$value[ $key ] = '0';
			} else {
				$value[ $key ] = '1';
			}
		}
		// Return value.
		return $value;
	}
}

add_action( 'plugins_loaded', array( 'DLM_Advanced_Settings', 'get_instance' ) );
