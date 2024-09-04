<?php
/**
 * Plugin Name: Classifieds Plugin
 * Description: A plugin to add classifieds functionality to your WordPress site.
 * Version: 1.0
 * Author: Facundo Montiel
 *
 * @package classifieds
 */

// Evitar el acceso directo
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Incluir archivos necesarios
require_once plugin_dir_path( __FILE__ ) . 'includes/post-types.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/taxonomies.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';

// Activación del plugin
function classifieds_plugin_activate() {
	create_classifieds_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'classifieds_plugin_activate' );

// Desactivación del plugin
function classifieds_plugin_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'classifieds_plugin_deactivate' );

// Encolar la hoja de estilos
function classifieds_plugin_enqueue_styles() {
	wp_enqueue_style( 'classifieds-styles', plugin_dir_url( __FILE__ ) . 'css/classifieds-styles.css' );
}
add_action( 'wp_enqueue_scripts', 'classifieds_plugin_enqueue_styles' );
