<?php

/**
 * Class AWFNErrors
 *
 * Simply displays the contents of any error logs in the the logs directory with the option to erase
 */
class AWFNErrors {
	private $awfn_errors_options;
	private $files;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'awfn_errors_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'awfn_errors_page_init' ) );
	}

	public function awfn_errors_add_plugin_page() {
		add_options_page(
			'AWFN Errors', // page_title
			'AWFN Errors', // menu_title
			'manage_options', // capability
			'awfn-errors', // menu_slug
			array( $this, 'awfn_errors_create_admin_page' ) // function
		);
	}

	public function awfn_errors_create_admin_page() {
		$this->awfn_errors_options = get_option( 'awfn_errors_option_name' ); ?>

		<div class="wrap">

			<h2>AWFN Errors</h2>
			<p>Generated Errors</p>
			<?php settings_errors(); ?>
			<button id="awfn-clear-log">Clear Log</button>
			<?php $this->display_log(); ?>

		</div>
	<?php }

	private function display_log() {

		$files = self::get_log_files();

		if ( ! empty( $files ) ) {

			echo '<div  id="awfn-error-logs">';

			foreach ( $files as $key => $filename ) {

				echo '<h2>' . strtoupper( basename( $filename ) ) . '</h2>';
				$handle = fopen( $filename, 'r' );
				if ( $handle ) {
					while ( false !== $line = fgets( $handle ) ) {
						echo '<p>' . $line . '</p>';
					}
					fclose( $handle );
				}
				echo '</div>';
			}
		} else {
			echo '<h2>' . __( 'No Logs Found', Adds_Weather_Widget::get_widget_slug() ) . '</h2>';
		}
	}

	public function awfn_errors_page_init() {
		register_setting(
			'awfn_errors_option_group', // option_group
			'awfn_errors_option_name', // option_name
			array( $this, 'awfn_errors_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'awfn_errors_setting_section', // id
			'Settings', // title
			array( $this, 'awfn_errors_section_info' ), // callback
			'awfn-errors-admin' // page
		);

		add_settings_field(
			'awfn_0', // id
			'awfn', // title
			array( $this, 'awfn_0_callback' ), // callback
			'awfn-errors-admin', // page
			'awfn_errors_setting_section' // section
		);
	}

	public function awfn_errors_sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['awfn_0'] ) ) {
			$sanitary_values['awfn_0'] = sanitize_text_field( $input['awfn_0'] );
		}

		return $sanitary_values;
	}

	public function awfn_errors_section_info() {

	}

	public function awfn_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="awfn_errors_option_name[awfn_0]" id="awfn_0" value="%s">',
			isset( $this->awfn_errors_options['awfn_0'] ) ? esc_attr( $this->awfn_errors_options['awfn_0'] ) : ''
		);
	}

	/**
	 * AJAX call to clear log files
	 */
	public static function clear_log() {
		$nonce = $_POST['secure'];
		if ( wp_verify_nonce( $nonce, 'awfn_clear_logs' ) ) {

			$files = self::get_log_files();

			if ( ! empty( $files ) ) {

				foreach ( $files as $key => $filename ) {

					$handle = fopen( $filename, 'r+' );
					if ( $handle ) {
						ftruncate( $handle, 0 );
						fclose( $handle );
					}
				}
			}

			wp_send_json_success( 'CLEARED' );
		}

	}

	/**
	 * Retrieve any log files in the logs directory
	 * @return array log files
	 */
	private static function get_log_files() {
		$dir      = new RecursiveDirectoryIterator( plugin_dir_path( dirname( __FILE__ ) ) . 'logs', RecursiveDirectoryIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $dir, RecursiveIteratorIterator::SELF_FIRST );

		# We create our list of files in the theme
		$files = array();
		foreach ( $iterator as $file ) {
			if ( substr( $file->getFilename(), - 4 ) == '.log' ) {
				array_push( $files, $file->getPathname() );
			}
		}

		return $files;
	}

}

/**
 * this only happens in admin and if AWFN_DEBUG is true
 */
$debug = ( defined( 'AWFN_DEBUG' ) && AWFN_DEBUG ) ? true : false;
if ( is_admin() && $debug ) {
	$awfn_errors = new AWFNErrors();
}

/* 
 * Retrieve this value with:
 * $awfn_errors_options = get_option( 'awfn_errors_option_name' ); // Array of All Options
 * $awfn_0 = $awfn_errors_options['awfn_0']; // awfn
 */
