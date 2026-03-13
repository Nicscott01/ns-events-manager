<?php
/**
 * Plugin Name: NS Events Manager
 * Plugin URI:  https://github.com/nscott/ns-events-manager
 * Description: Lightweight events CPT with ACF fields. No single-post pages — events link out to external URLs. Built for Breakdance post loop builders.
 * Version:     1.0.2
 * Author:      Nic Scott
 * License:     GPL-2.0-or-later
 * Text Domain: ns-events-manager
 */

defined( 'ABSPATH' ) || exit;

define( 'NS_EM_VERSION', '1.0.2' );
define( 'NS_EM_DIR', plugin_dir_path( __FILE__ ) );
define( 'NS_EM_URL', plugin_dir_url( __FILE__ ) );

require_once NS_EM_DIR . 'includes/class-rewrites.php';
require_once NS_EM_DIR . 'includes/class-cpt.php';
require_once NS_EM_DIR . 'includes/class-acf-fields.php';
require_once NS_EM_DIR . 'includes/class-settings.php';

/**
 * Returns plugin options with defaults merged in.
 */
function ns_em_options(): array {
	$defaults = [
		'singular_label'         => 'Event',
		'plural_label'           => 'Events',
		'menu_label'             => 'Events',
		'cpt_slug'               => 'events',
		'menu_icon'              => 'dashicons-calendar-alt',
		'enable_rest'            => '1',
		'enable_end_date'        => '0',
		'enable_venue_name'      => '1',
		'enable_capacity'        => '0',
		'enable_featured'        => '0',
		'enable_full_desc'       => '1',
		'default_link_label'     => 'Learn More',
	];

	$saved = get_option( 'ns_events_manager_options', [] );

	return wp_parse_args( $saved, $defaults );
}

register_activation_hook( __FILE__, [ 'NS_EM_Rewrites', 'flush' ] );
register_deactivation_hook( __FILE__, [ 'NS_EM_Rewrites', 'flush' ] );

add_action( 'plugins_loaded', function () {
	NS_EM_Settings::init();
	NS_EM_CPT::init();
	NS_EM_ACF_Fields::init();
} );
