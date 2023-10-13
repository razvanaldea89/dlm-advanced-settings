<?php
	/*
		Plugin Name: Download Monitor - Advanced Settings
		Plugin URI: https://github.com/razvanaldea89/dlm-advanced-settings
		Description: This plugin taps into Download Monitor's hooks and offers a way to manipulate them via the admin panel.
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
				'label'   => 'Delete files when deleting a download',
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
			),
			'dlm_hide_meta_version'     => array(
				'label'   => 'Hide meta version in header',
				'default' => '0',
				'params'  => 1,
				'type'    => 'checkbox',
			),
			'dlm_count_meta_downloads'  => array(
				'label'   => 'Add meta value to download count',
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
			),
			'dlm_do_xhr'                => array(
				'label'   => 'Enable XHR downloads',
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
			),
			'dlm_reports_server_limits' => array(
				'label'   => 'Reports server limits',
				'default' => $this->php_info,
				'params'  => 1,
				'type'    => 'multi_text',
			),
			'dlm_xhr_progress'          => array(
				'label'   => 'XHR progress bar',
				'default' => array(
					'display'   => true,
					'animation' => includes_url( '/images/spinner.gif' ),
				),
				'params'  => 1,
				'type'    => 'multi_text',
			),
			'dlm_timestamp_link'        => array(
				'label'   => 'Show a timestamp in the download link',
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
			),
			'dlm_restricted_file_types' => array(
				'label'   => 'Restricted file types',
				'default' => '',
				'params'  => 2,
				'type'    => 'text',
			),
			'dlm_enable_reports'        => array(
				'label'   => 'Enable reports',
				'default' => '1',
				'params'  => 1,
				'type'    => 'checkbox',
			),
			'dlm_404_redirect'          => array(
				'label'   => '404 redirect',
				'default' => '',
				'params'  => 1,
				'type'    => 'text',
			),

		);

		$defaults = array();
		foreach ( $this->hooks as $key => $setting ) {
			$defaults[ $key ] = $setting['default'];
		}

		$this->settings = wp_parse_args( get_option( 'dlm-as-settings', array() ), $defaults );

		add_filter( 'dlm_admin_menu_links', array( $this, 'add_submenu_page' ), 120 );
		add_action( 'init', array( $this, 'set_hooks' ) );

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
			'priority'   => 160,
		);

		return $links;
	}

	/**
	 * Render the submenu page
	 *
	 * @since 1.0.0
	 */
	public function render_submenu_page() {

		if ( isset( $_POST['dlm-as-settings'] ) ) {
			update_option( 'dlm-as-settings', $_POST['dlm-as-settings'] );
		}

		?>
			<div class="wrap">
				<h2>Advanced Settings</h2>
				<form method="post"
					  action="<?php echo admin_url( 'edit.php?post_type=dlm_download&page=dlm-advanced-settings' ); ?>">
				<?php
					$html = '';
				foreach ( $this->hooks as $hook => $option ) {
					$html .= '<div class="dlm-as-setting">';
					switch ( $option['type'] ) {
						case 'checkbox':
							$html .= '<div class="wpchill-toggle">
									<input class="wpchill-toggle__input" type="checkbox" name="dlm-as-settings[' . $hook . ']" value="1" ' . checked( $this->settings[ $hook ], '1', false ) . ' />
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
								</div>';
							$html .= '<label>' . esc_html( $option['label'] ) . '</label>';
							break;
						case 'text':
							$html .= '<input type="text" name="dlm-as-settings[' . esc_attr( $hook ) . ']" value="' . esc_attr( $this->settings[ $hook ] ) . '" placeholder="' . esc_attr( $option['default'] ) . '" />';
							$html .= '<label for="dlm-as-settings[' . esc_attr( $hook ) . ']">' . esc_html( $option['label'] ) . '</label>';
							break;
						case 'multi_text':
							$html .= $option['label'] . '<br />';
							foreach ( $option['default'] as $key => $value ) {
								$html .= '<p>';
								$html .= '<input type="text" name="dlm-as-settings[' . esc_attr( $hook ) . ' ][' . esc_attr( $key ) . ']" value="' . esc_attr( $this->settings[ $hook ][ $key ] ) . '" placeholder="' . esc_attr( $value ) . '" />';
								$html .= '<label for="dlm-as-settings[' . esc_attr( $hook ) . ' ][' . esc_attr( $key ) . ']">' . esc_html( $key ) . '</label>';
								$html .= '</p>';
							}
							break;
					}
					$html .= '</div>';
				}
					echo $html;
				?>
					<button type="submit">Save changes</button>
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
					<strong>Download Monitor - Advanced Settings</strong> requires <strong>Download Monitor</strong> to
					be installed and activated.
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
	public function set_hooks() {

		// Check if we have settings.
		if ( empty( $this->settings ) ) {
			return;
		}
		// Cycle through settings.
		foreach ( $this->settings as $key => $value ) {
			if ( ! isset( $this->hooks[ $key ] ) ) {
				continue;
			}

			add_filter(
				$key,
				array( $this, 'filter_' . $key ),
				15,
				$this->hooks[ $key ]['params']
			);
		}
	}

	/**
	 * Filter for reports server limits
	 *
	 * @param array $value The default value.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function filter_dlm_reports_server_limits( $value ) {

		$value['retrieved_rows']        = $this->settings['dlm_reports_server_limits']['retrieved_rows'];
		$value['retrieved_user_data']   = $this->settings['dlm_reports_server_limits']['retrieved_user_data'];
		$value['retrieved_chart_stats'] = $this->settings['dlm_reports_server_limits']['retrieved_chart_stats'];
		$value['max_execution_time']    = $this->settings['dlm_reports_server_limits']['max_execution_time'];
		$value['memory_limit']          = $this->settings['dlm_reports_server_limits']['memory_limit'];

		return $value;
	}

	/**
	 * Filter for XHR progress bar
	 *
	 * @param array $value The default value.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function filter_dlm_xhr_progress( $value ) {
		$value['display']   = $this->settings['dlm_xhr_progress']['display'];
		$value['animation'] = $this->settings['dlm_xhr_progress']['animation'];

		return $value;
	}

	/**
	 * Filter for 404 redirect
	 *
	 * @param string $value The default value.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function filter_dlm_404_redirect( $value ) {
		return $this->settings['dlm_404_redirect'];
	}

	/**
	 * Filer for restricted file types
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_restricted_file_types( $value ) {
		return $this->settings['dlm_restricted_file_types'];
	}

	/**
	 * Filter for Reports
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_enable_reports( $value ) {
		return isset( $this->settings['dlm_enable_reports'] ) && '1' === $this->settings['dlm_enable_reports'];
	}

	/**
	 * Filter timestamp link
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_timestamp_link( $value ) {
		return isset( $this->settings['dlm_timestamp_link'] ) && '1' === $this->settings['dlm_timestamp_link'];
	}

	/**
	 * Filter for XHR downloads
	 *
	 * @param bool $value The default value.
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function filter_dlm_do_xhr( $value ) {
		return isset( $this->settings['dlm_do_xhr'] ) && '1' === $this->settings['dlm_do_xhr'];
	}

	/**
	 * Filter for meta version in header
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_count_meta_downloads( $value ) {
		return isset( $this->settings['dlm_count_meta_downloads'] ) && '1' === $this->settings['dlm_count_meta_downloads'];
	}

	/**
	 * Filter for meta version in header
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_hide_meta_version( $value ) {
		return isset( $this->settings['dlm_hide_meta_version'] ) && '1' === $this->settings['dlm_hide_meta_version'];
	}

	/**
	 * Filter for delete files when deleting a download
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_delete_files( $value ) {
		return isset( $this->settings['dlm_delete_files'] ) && '1' === $this->settings['dlm_delete_files'];
	}
}

	add_action( 'plugins_loaded', array( 'DLM_Advanced_Settings', 'get_instance' ) );
