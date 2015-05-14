<?php
/**
 * Slider CC
 *
 * @package   ChChSlider
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

if ( ! class_exists( 'ChChSliderTemplate' ) )
    require_once( CHCH_SF_PLUGIN_DIR . 'public/includes/chch-slider-template.php' );
	
/**
 * @package ChChSlider
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class ChChSlider {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.3';

	/** 
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'ch-ch-slider';
	 
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() { 
		
		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) ); 
  		
		// Include public fancing styles and scripts
		add_action( 'wp_enqueue_scripts', array($this,'chch_sf_template_scripts') );
		
		// Include fonts on front-end
		add_action('wp_head', array( $this, 'chch_sf_hook_fonts' ) );  
		
		 add_shortcode('slidercc', array( $this, 'chch_sf_shortcode' ) );
	}
	
	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		
	} 
	 
	/**
	 * Get All Active Sliders IDs
	 *
	 * @since  1.0.0
	 *
	 * @return   array - Sliders ids
	 */
	private function get_sliders() {
		$list = array();
		
		$args = array(
			'post_type' => 'chch-slider',
			'posts_per_page' => -1, 
			'post_status' => 'publish'
		);
		
		$sliders = get_posts( $args);
		
		if ( $sliders ) {
			foreach ( $sliders as $slider ) {
				$list[] = $slider->ID;
			}
		} 	 
		return $list;
	}
	
	/**
	 * Include Templates scripts on Front-End
	 *
	 * @since  0.1.0
	 *
	 * @return   array - Slider CCs ids
	 */
	function chch_sf_template_scripts() { 
		
		$sliders = $this->get_sliders(); 
		if(!empty($sliders)) {
			foreach($sliders as $id) { 
				$templates = new ChChSliderTemplate(NULL,  $id);	
				$templates->enqueue_template_style();
			}
		}
			
	} 
	
	
	/**
	 * Include fonts on front-end
	 *
	 * @since  0.1.0
	 */
	function chch_sf_hook_fonts() {
	
		$output="<link href='http://fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Lora:400,700|Open+Sans:400,300,700|Oswald:700,300|Roboto:400,700,300|Signika:400,700,300' rel='stylesheet' type='text/css'>";
	
		echo $output;
	}
	
	function chch_sf_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0, 
		), $atts, 'slidercc' );
		
		if($atts['id'] != 0 && get_post_status ($atts['id']) == 'publish') {
			$template = new ChChSliderTemplate(NULL, $atts['id']);
			  
			ob_start();
			echo $template->build_css();  
			$template->get_template();
			echo $template->build_js(); 
			$slider =  ob_get_clean();
			return $slider;
			 
		} else {
			return '';	
		}
		 
	} 
}
