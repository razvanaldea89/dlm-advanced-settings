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
		$value['retrieved_rows']        = ( ! empty( $this->settings['dlm_reports_server_limits']['retrieved_rows'] ) ) ? $this->settings['dlm_reports_server_limits']['retrieved_rows'] : $value['retrieved_rows'];
		$value['retrieved_user_data']   = ( ! empty( $this->settings['dlm_reports_server_limits']['retrieved_user_data'] ) ) ? $this->settings['dlm_reports_server_limits']['retrieved_user_data'] : $value['retrieved_user_data'];
		$value['retrieved_chart_stats'] = ( ! empty( $this->settings['dlm_reports_server_limits']['retrieved_chart_stats'] ) ) ? $this->settings['dlm_reports_server_limits']['retrieved_chart_stats'] : $value['retrieved_chart_stats'];
		$value['max_execution_time']    = ( ! empty( $this->settings['dlm_reports_server_limits']['max_execution_time'] ) ) ? $this->settings['dlm_reports_server_limits']['max_execution_time'] : $value['max_execution_time'];
		$value['memory_limit']          = ( ! empty( $this->settings['dlm_reports_server_limits']['memory_limit'] ) ) ? $this->settings['dlm_reports_server_limits']['memory_limit'] : $value['memory_limit'];

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
		$value['display']   =  ! empty( $this->settings['dlm_xhr_progress']['display'] ) ? $this->settings['dlm_xhr_progress']['display'] : $value['display'];
		$value['animation'] = ! empty( $this->settings['dlm_xhr_progress']['animation'] ) ? $this->settings['dlm_xhr_progress']['animation'] : $value['animation'];
		
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
		if ( empty( $this->settings['dlm_404_redirect'] ) ) {
			return $value;
		}

		// Return the 404 redirect URL.
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
		if ( empty( $this->settings['dlm_placeholder_image_src'] ) ) {
			return $value;
		}

		// Return the image src used for the placeholder.
		return $this->settings['dlm_placeholder_image_src'];
	}

	/**
	 * Filer for restricted file types
	 *
	 * @param array $value The default value.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function filter_dlm_restricted_file_types( array $value ) {
		// If the data from the setting is empty return the default value.
		if ( empty( $this->settings['dlm_restricted_file_types'] ) ) {
			return $value;
		}
		// The new files should be string with every file separated by a comma. Make an array from them.
		$new_files = explode( ',', $this->settings['dlm_restricted_file_types'] );
	
		// Send the merge between $value and $new_files.
		return array_merge( $value, $new_files );
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_enable_reports'] ) && '1' === $this->settings['dlm_enable_reports'] ) {
			return true;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_timestamp_link'] ) && '0' === $this->settings['dlm_timestamp_link'] ) {
			return false;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_do_xhr'] ) && '0' === $this->settings['dlm_do_xhr'] ) {
			return false;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_count_meta_downloads'] ) && '0' === $this->settings['dlm_count_meta_downloads'] ) {
			return false;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_delete_files'] ) && '1' === $this->settings['dlm_delete_files'] ) {
			return true;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_x_sendfile'] ) && '1' === $this->settings['dlm_x_sendfile'] ) {
			return true;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_hotlink_protection'] ) && '1' === $this->settings['dlm_hotlink_protection'] ) {
			return true;
		}

		return $value;
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
		// IF setting is set return true.
		if ( isset( $this->settings['dlm_allow_x_forwarded_for'] ) && '1' === $this->settings['dlm_allow_x_forwarded_for'] ) {
			return true;
		}

		return $value;
	}
}
