<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/**
 * @package   Aviation Weather from NOAA
 * @author    Mark Chouinard <mark@chouinard.me>
 * @license   GPL-2.0+
 * @link      https://github.com/machouinard/aviation-weather-from-noaa
 * @copyright 2015 Me, you and all the others
 *
 * @wordpress-plugin
 * Plugin Name:       Aviation Weather From NOAA
 * Plugin URI:        https://github.com/machouinard/aviation-weather-from-noaa
 * Description:       Aviation weather data from NOAA's Aviation Digital Data Service (ADDS)
 * Version:           0.7.2
 * Author:            Mark Chouinard
 * Author URI:        https://chouinard.me
 * Text Domain:       awfn
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /lang
 * GitHub Plugin URI: https://github.com/machouinard/aviation-weather-from-noaa
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PLUGIN_ROOT' ) ) {
	define( 'PLUGIN_ROOT', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'STORED_STATIONS_KEY' ) ) {
	define( 'STORED_STATIONS_KEY', 'awfn-stored-stations' );
}
if ( ! defined( 'SHORTCODE_SLUG' ) ) {
	define( 'SHORTCODE_SLUG', 'awfn_' );
}
if ( ! defined( 'EXPIRE_TIME' ) ) {
	define( 'EXPIRE_TIME', 1800 );
}

require_once 'vendor/autoload.php';
require_once PLUGIN_ROOT . 'admin/class-awfn-logs.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once dirname( __FILE__ ) . '/admin/class-awfn-cli.php';
}

// Require our classes
$classes = glob( plugin_dir_path( __FILE__ ) . 'classes/*.php' );
rsort( $classes );
foreach ( $classes as $class ) {
	require_once $class;
}

/**
 * Class Adds_Weather_Widget
 *
 *
 *
 * @package Aviation Weather from NOAA
 * @since   0.4.0
 * @access  public
 */
class Adds_Weather_Widget extends WP_Widget {

	/**
	 *
	 * Unique identifier.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected static $widget_slug = 'aviation-weather-from-noaa';
	protected static $shortcode_slug = 'awfn_';

	public static $expire_time = 1800;
	public static $log;
	private $awfn_debug;
	private $prefix;


	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		$awfn_logs_options = get_option( 'awfn_logs_option_name' );
		$debug_0           = isset( $awfn_logs_options['debug_0'] ) ? true : false;

		$this->awfn_debug = $debug_0;

		//* Unless we're debugging, enqueue minified scripts
		$this->prefix = $this->awfn_debug ? '' : '-min';

		//* load plugin text domain
		add_action( 'init', array( $this, 'widget_textdomain' ) );

		//* Hook to widget delete so we can delete transient when widget is deleted
		add_action( 'sidebar_admin_setup', array( $this, 'awfn_sidebar_admin_setup' ) );

		//* Hook up our AJAX functions
		add_action( 'wp_ajax_weather_shortcode', array( 'AWFN_Shortcode', 'ajax_weather_shortcode' ) );
		add_action( 'wp_ajax_nopriv_weather_shortcode', array( 'AWFN_Shortcode', 'ajax_weather_shortcode' ) );
		add_action( 'wp_ajax_weather_widget', array( 'Adds_Weather_Widget', 'ajax_weather_widget' ) );
		add_action( 'wp_ajax_nopriv_weather_widget', array( 'Adds_Weather_Widget', 'ajax_weather_widget' ) );
		add_action( 'wp_ajax_awfn_clear_log', array( 'AWFNLogs', 'clear_log' ) );
		add_action( 'in_plugin_update_message-aviation-weather-from-noaa/aviation-weather-from-noaa.php', [$this, 'show_upgrade_notice'], 10, 2 );

		//* Hooks fired when the Widget is activated and deactivated
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

		parent::__construct(
			self::get_widget_slug(),
			__( 'Aviation Weather Info', self::get_widget_slug() ),
			array(
				'classname'   => 'machouinard_adds_widget_class',
				'description' => __( "Displays METAR & other info from NOAA's Aviation Digital Data Service", self::get_widget_slug() )
			)
		);

		//* Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		//* Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_ajax_scripts' ) );

		//* Gutenberg
		add_action( 'enqueue_block_editor_assets', array( $this, 'awfn_block_editor_assets' ) );

		//* Add shortcode
		add_shortcode( 'adds_weather', array( 'AWFN_Shortcode', 'adds_weather_shortcode' ) );

	} //* end constructor

	/**
	 * Return the widget slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public static function get_widget_slug() {
		return self::$widget_slug;
	}

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs a static wrapper for the widget content.
	 * Includes necessary details to be picked up and processed by JS/AJAX.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		$defaults = array(
			'icao'              => 'KSMF',
			'hours'             => '2',
			'show_metar'        => true,
			'show_taf'          => true,
			'show_pireps'       => true,
			'show_station_info' => true,
			'radial_dist'       => '100',
			'title'             => ''
		);

		$instance              = wp_parse_args( $instance, $defaults );
		$instance['widget_id'] = $this->id;
		$spinner_url           = plugin_dir_url( __FILE__ ) . 'css/loading.gif';
		$instance['spinner']   = $spinner_url;
		extract( $args, EXTR_SKIP );

		echo $before_widget;

		?>

		<section class='adds-weather-wrapper' data-instance='<?php echo json_encode( $instance ); ?>'><img
				id="<?php echo $this->id; ?>-loading" src="<?php echo $spinner_url; ?>"/></section>
		<?php
		echo $after_widget;


	} //* end widget

	/**
	 * Outputs the content of the widget.
	 * This is done using AJAX so our widget is not affected by page caching.
	 */
	public static function ajax_weather_widget() {

		check_ajax_referer( 'widget-ajax', 'security' );

		//* Coming from our jQuery/AJAX POST
		$instance = $_POST['instance'];

		$hours             = absint( $instance['hours'] ) <= 6 ? absint( $instance['hours'] ) : 1;
		$show_metar        = filter_var( $instance['show_metar'], FILTER_VALIDATE_BOOLEAN );
		$show_taf          = filter_var( $instance['show_taf'], FILTER_VALIDATE_BOOLEAN );
		$show_pireps       = filter_var( $instance['show_pireps'], FILTER_VALIDATE_BOOLEAN );
		$show_station_info = filter_var( $instance['show_station_info'], FILTER_VALIDATE_BOOLEAN );
		$distance          = absint( $instance['radial_dist'] );
		$widget_id         = $instance['widget_id'];

		//* Check if there is a cached output
		$cache = get_transient( $widget_id );

		if ( $cache ) {
			self::log( 'info', 'Cached data found for Widget ID: ' . $widget_id );
			//* If we have good cached data, use it.
			wp_send_json_success( $cache );
		}

		self::log( 'info', 'No cached data found for Widget ID: ' . $widget_id );

		$station = new AwfnStation( $instance['icao'], $show_station_info );
		$icao    = $station->station_exist() ? (string) $station->get_icao() : false;
		$title   = empty( $instance['title'] ) ? sprintf( _n( 'Available data for %s from the past hour',
			'Available data for %s from the past %d hours', $hours, self::get_widget_slug() ), $icao,
			$hours ) : $instance['title'];

		//* No point going any further without ICAO
		if ( ! $icao ) {
			return;
		}

		$widget_string = '';

		ob_start();


		if ( $station->station_exist() ) {
			echo '<header>' . esc_html( $title ) . '</header>';

			$station->decode_data();
			$station->build_display();
			$station->display_data();

			$metar = new AwfnMetar( $icao, $hours, $show_metar );
			$metar->go();

			$taf = new AwfnTaf( $icao, $hours, $show_taf );
			$taf->go();

			$pirep = new AwfnPirep( $station->get_icao(), $station->lat(), $station->lng(), $distance, $hours, $show_pireps );
			$pirep->go();
		} else {
			echo '<header class="awfn-no-station">ICAO ' . esc_html( $icao ) . ' not found.</header>';
		}

		$widget_string .= ob_get_clean();

		set_transient( $widget_id, $widget_string, EXPIRE_TIME );

		wp_send_json_success( $widget_string );

	}

	/**
	 * Runs on widget deletion
	 * Widget transient is being stored using widget id, so we can easily remove it here
	 */
	function awfn_sidebar_admin_setup() {

		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) ) {

			$widget_id = $_POST['widget-id'];

			if ( isset( $_POST['delete_widget'] ) ) {
				if ( 1 === (int) $_POST['delete_widget'] ) {
					delete_transient( $widget_id );
				}
			}

		}

	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['icao']              = AwfnStation::static_clean_icao( $new_instance['icao'] );
		$instance['hours']             = absint( $new_instance['hours'] );
		$instance['show_metar']        = (bool) $new_instance['show_metar'];
		$instance['show_taf']          = (bool) $new_instance['show_taf'];
		$instance['show_pireps']       = (bool) $new_instance['show_pireps'];
		$instance['show_station_info'] = (bool) $new_instance['show_station_info'];
		$instance['radial_dist']       = absint( $new_instance['radial_dist'] );
		$instance['title']             = sanitize_text_field( $new_instance['title'] );

		//* Delete old transient data
		delete_transient( $this->id );

		return $instance;

	} //* end widget


	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$defaults          = array(
			'icao'              => 'KSMF',
			'hours'             => 2,
			'show_metar'        => true,
			'show_taf'          => true,
			'show_pireps'       => true,
			'show_station_info' => true,
			'radial_dist'       => '100',
			'title'             => '',
		);
		$instance          = wp_parse_args(
			(array) $instance,
			$defaults
		);
		$icao              = $instance['icao'];
		$hours             = absint( $instance['hours'] );
		$show_metar        = (bool) $instance['show_metar'];
		$show_taf          = (bool) $instance['show_taf'];
		$show_pireps       = (bool) $instance['show_pireps'];
		$show_station_info = (bool) $instance['show_station_info'];
		$radial_dist       = absint( $instance['radial_dist'] );
		$title             = sanitize_text_field( $instance['title'] );

		//* Display the admin form
		include( plugin_dir_path( __FILE__ ) . 'views/admin.php' );

	} //* end form

	/*--------------------------------------------------*/
	/* Public Functions
	/*--------------------------------------------------*/

	/**
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function widget_textdomain() {

		load_plugin_textdomain( self::get_widget_slug(), false, plugin_dir_path( __FILE__ ) . 'lang/' );

	} //* end widget_textdomain

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param  boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		//* TODO: define activation functionality here
	} //* end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * Deletes widget and shortcode transients when plugin is disabled
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 *
	 * @since 0.4.0
	 */
	public function deactivate( $network_wide ) {

		global $wpdb;

		//* TODO: ensure that's the right way to reference options table in multisite
		if ( function_exists( 'is_multisite' ) && is_multisite() && $network_wide ) {
			$current = $wpdb->blogid;
			$blogs   = $wpdb->get_col( "SELECT bog_id FROM {$wpdb->blogs}" );
			foreach ( $blogs as $blog ) {
				switch_to_blog( $blog );
				$keys = array( self::$shortcode_slug, self::$widget_slug . '-' );
				foreach ( $keys as $key ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s;",
						'%' . $wpdb->esc_like( $key ) . '%' ) );
				}
			}
			switch_to_blog( $current );
		} else {
			$keys = array( self::$shortcode_slug, self::$widget_slug . '-' );
			foreach ( $keys as $key ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s;",
					'%' . $wpdb->esc_like( $key ) . '%' ) );
			}
		}
	} //* end deactivate

	/**
	 * Delete airport data stroed in options table when plugin is deleted
	 *
	 * @since 0.4.0
	 */
	public static function uninstall() {
		delete_option( STORED_STATIONS_KEY );
	}

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		wp_enqueue_style( self::get_widget_slug() . '-admin-styles', plugins_url( 'css/awfn-admin.css', __FILE__ ) );

	} //* end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		wp_enqueue_script( self::get_widget_slug() . '-admin-script', plugins_url( "js/admin{$this->prefix}.js", __FILE__ ), array( 'jquery' ) );
		$nonce = wp_create_nonce( 'awfn_clear_logs' );
		wp_localize_script( self::get_widget_slug() . '-admin-script', 'options', array(
			'secure'     => $nonce,
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'awfn_debug' => $this->awfn_debug
		) );

	} //* end register_admin_scripts

	/**
	 * Registers and enqueues widget-specific styles.
	 * Allows for overriding styles with custom CSS in theme/child theme
	 */
	public function register_widget_styles() {

		//* Check child theme
		$file = 'css/aviation_weather_from_noaa.css';
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$location = trailingslashit( get_stylesheet_directory_uri() ) . $file;

			//* Check parent theme
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$location = trailingslashit( get_template_directory_uri() ) . $file;

			//* use our style
		} else {
			$location = plugins_url( "/css/aviation_weather_from_noaa.css", __FILE__ );
		}
		wp_enqueue_style( self::get_widget_slug() . '-widget-styles', apply_filters( 'adds_custom_css', $location ) );

		wp_enqueue_script( 'font-awesome', '//kit.fontawesome.com/a9c93912bd.js' );

	} //* end register_widget_styles

	/**
	 * Registers and enqueues widget-specific scripts.
	 */
	public function register_widget_scripts() {

		wp_enqueue_script( self::get_widget_slug() . '-script', plugins_url( "js/widget{$this->prefix}.js", __FILE__ ), array(
			'jquery'
		) );
		wp_localize_script( self::get_widget_slug() . '-script', 'ajax_url', admin_url( 'admin-ajax.php' ) );


	} //* end register_widget_scripts

	public function register_ajax_scripts() {

		wp_enqueue_script( self::get_widget_slug() . '-sc-ajax', plugins_url( "js/shortcode-ajax{$this->prefix}.js", __FILE__ ), array( 'jquery' ) );
		$shortcode_nonce = wp_create_nonce( 'shortcode-ajax' );
		wp_localize_script( self::get_widget_slug() . '-sc-ajax', 'shortcodeOptions', array(
			'awfn_debug' => $this->awfn_debug,
			'security'   => $shortcode_nonce
		) );

		wp_enqueue_script( self::get_widget_slug() . '-w-ajax', plugins_url( "js/widget-ajax{$this->prefix}.js", __FILE__ ), array( 'jquery' ) );
		$widget_nonce = wp_create_nonce( 'widget-ajax' );
		wp_localize_script( self::get_widget_slug() . '-w-ajax', 'widgetOptions', array(
			'awfn_debug' => $this->awfn_debug,
			'security'   => $widget_nonce,
			'test'       => 'fuck'
		) );

	}

	public static function log( $severity, $msg ) {
		$awfn_logs_options = get_option( 'awfn_logs_option_name' );
		$debug_0           = isset( $awfn_logs_options['debug_0'] ) ? true : false;

		if ( $debug_0 ) {
			$logger = self::get_logger();
//			if ( null !== $logger ) {
				if ( is_array( $msg ) ) {
					$logger->$severity( print_r( $msg ) );
				} else {
					$logger->$severity( $msg );
				}
//			}
		}
	}

	protected static function get_logger() {
		if ( null === self::$log ) {
			self::setup_logger();
		}

		return self::$log;
	}

	protected static function setup_logger() {
		//* Prepare logger
		$dev_log_dir = PLUGIN_ROOT . 'logs';

		//* Permissions for his one are up to you, for now. Sorry.
		if ( ! file_exists( $dev_log_dir ) ) {
			mkdir( $dev_log_dir, 0700, true );
		}
		$prod_log_dir = PLUGIN_ROOT . 'logs';
		if ( ! file_exists( $prod_log_dir ) ) {
			mkdir( $prod_log_dir, 0700, true );
		}
		self::$log       = new Logger( 'AJAX' );
		$formatter       = new LineFormatter( "[%datetime%] > %channel%.%level_name%: %message%\n" );
		$info_handler    = new StreamHandler( PLUGIN_ROOT . 'logs/info.log', Logger::INFO, false );
		$debug_handler   = new StreamHandler( PLUGIN_ROOT . 'logs/debug.log', Logger::DEBUG );
		$warning_handler = new StreamHandler( PLUGIN_ROOT . 'logs/warning.log', Logger::WARNING, false );
		$info_handler->setFormatter( $formatter );
		$debug_handler->setFormatter( $formatter );
		$warning_handler->setFormatter( $formatter );
		self::$log->pushHandler( $debug_handler );
		self::$log->pushHandler( $info_handler );
		self::$log->pushHandler( $warning_handler );
	}

	/**
	 * Enqueue Block JS
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function awfn_block_editor_assets() {
		$assets = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php' );

		wp_enqueue_script(
			'awfn-block-js',
			plugins_url( 'build/index.js', __FILE__ ),
			$assets['dependencies'],
			$assets['version']
		);

		$spinner_url     = plugin_dir_url( __FILE__ ) . 'css/loading.gif';
		wp_localize_script( 'awfn-block-js', 'opts', ['spinnerUrl' => $spinner_url] );
	}

	/**
	 * Show upgrade notice on plugin page
	 *
	 * This just makes sure the updgrade notice appears so users can see what's changed.
	 * Normally it only appears on the upgrade page.
	 *
	 * @param $current
	 * @param $new
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function show_upgrade_notice( $current, $new ) {
		if ( isset( $new->upgrade_notice ) && strlen( trim( $new->upgrade_notice ) ) > 0 ) {
			echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>Upgrade Notice:&nbsp;</strong>';
			echo strip_tags( $new->upgrade_notice);
		}
	}


} //* end class

//* Register widget
add_action( 'widgets_init', 'awfn_register_widget' );
function awfn_register_widget() {

	register_widget( "Adds_Weather_Widget" );

}
