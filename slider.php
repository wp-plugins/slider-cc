<?php
/**
 *
 * @package   ChChSlider
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 *
 * @wordpress-plugin
 * Plugin Name:       Slider CC
 * Plugin URI:        http://shop.chop-chop.org
 * Description:       An elegant Slider in just a few clicks.
 * Version:           1.0.4
 * Author:            Chop-Chop.org
 * Author URI:        http://chop-chop.org
 * Text Domain:       cc-slider-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
 
define( 'CHCH_SF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CHCH_SF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-chch-slider.php' ); 


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'ChChSlider', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ChChSlider', 'deactivate' ) );



add_action( 'plugins_loaded', array( 'ChChSlider', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
 
	
if (is_admin()) { 
	
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-chch-slider-admin.php' );
	add_action( 'plugins_loaded', array( 'ChChSliderAdmin', 'get_instance' ) );

}
