<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DLM_AS_Hooks {

	/**
	 * Plugin instance
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public static $instance = null;

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
	 * @param array $settings The settings.
	 *
	 * @since 1.0.0
	 */
	private function __construct( $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @param array $settings The settings.
	 *
	 * @return object The DLM_AS_Hooks object.
	 * @since 1.0.0
	 */
	public static function get_instance( $settings ) {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_AS_Hooks ) ) {
			self::$instance = new DLM_AS_Hooks( $settings );
		}

		return self::$instance;
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
		// If the data from the setting is empty return the default value.
		if ( empty( $this->settings['dlm_reports_server_limits'] ) ) {
			return $value;
		}
		// Return the new options.
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
		// If the data from the setting is empty return the default value.
		if ( empty( $this->settings['dlm_xhr_progress'] ) ) {
			return $value;
		}
		// Return the new options.
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
		// If the data from the setting is empty return the default value.
		if ( empty( $this->settings['dlm_restricted_file_types'] ) ) {
			return $value;
		}

		// Return the 404 redirect URL
		return $this->settings['dlm_404_redirect'];
	}

	/**
	 * Filter for placeholder image src
	 *
	 * @param string $value The default value.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function filter_dlm_placeholder_image_src( $value ) {
		// If the data from the setting is empty return the default value.
		if ( empty( $this->settings['dlm_restricted_file_types'] ) ) {
			return $value;
		}

		// Return the image src used for the placeholder
		return $this->settings['dlm_placeholder_image_src'];
	}

	/**
	 * Filer for restricted file types
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_restricted_file_types( array $value ) {
		// If the data from the setting is empty return the default value.
		if ( empty( $this->settings['dlm_restricted_file_types'] ) ) {
			return $value;
		}

		// Setting is a string with file types separated by a comma (","), so we need to create an array from it.
		return explode( ',', $this->settings['dlm_restricted_file_types'] );
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

	/**
	 * Filter for X-Accel-Redirect / X-Sendfile
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_x_sendfile( $value ) {
		return isset( $this->settings['dlm_x_sendfile'] ) && '1' === $this->settings['dlm_x_sendfile'];
	}

	/**
	 * Filter for preventing hot linking
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_hotlink_protection( $value ) {
		return isset( $this->settings['dlm_hotlink_protection'] ) && '1' === $this->settings['dlm_hotlink_protection'];
	}

	/**
	 * Filter for allowing proxy IP override
	 *
	 * @param bool $value The default value.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function filter_dlm_allow_x_forwarded_for( $value ) {
		return isset( $this->settings['dlm_allow_x_forwarded_for'] ) && '1' === $this->settings['dlm_allow_x_forwarded_for'];
	}
}
