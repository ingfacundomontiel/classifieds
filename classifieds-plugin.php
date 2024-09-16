<?php
/**
 * Plugin Name: Classifieds Plugin
 * Description: A plugin to add classifieds functionality to your WordPress site.
 * Version: 1.0
 * Author: Facundo Montiel
 *
 * @package classifieds
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include necessary files.
require_once plugin_dir_path( __FILE__ ) . 'includes/post-types.php'; // Include the file that registers post types.
require_once plugin_dir_path( __FILE__ ) . 'includes/taxonomies.php'; // Include the file that registers taxonomies.
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php'; // Include the file that registers shortcodes.

/**
 * Function to activate the Classifieds Plugin.
 * This function is triggered on plugin activation to create the post type and flush rewrite rules.
 *
 * @return void
 */
function classifieds_plugin_activate() {
	create_classifieds_post_type(); // Create the Classifieds post type.
	flush_rewrite_rules(); // Flush rewrite rules to register the post type.
}
register_activation_hook( __FILE__, 'classifieds_plugin_activate' ); // Hook the activation function to the plugin activation.

/**
 * Function to deactivate the Classifieds Plugin.
 * This function is triggered on plugin deactivation to flush rewrite rules.
 *
 * @return void
 */
function classifieds_plugin_deactivate() {
	flush_rewrite_rules(); // Flush rewrite rules on plugin deactivation.
}
register_deactivation_hook( __FILE__, 'classifieds_plugin_deactivate' ); // Hook the deactivation function to the plugin deactivation.

/**
 * Enqueue the Classifieds plugin's stylesheets.
 *
 * @return void
 */
function classifieds_plugin_enqueue_styles() {
	wp_enqueue_style( 'classifieds-styles', plugin_dir_url( __FILE__ ) . 'css/classifieds-styles.css' ); // Enqueue the plugin's CSS file.
}
add_action( 'wp_enqueue_scripts', 'classifieds_plugin_enqueue_styles' ); // Hook the styles to be enqueued on the frontend.

/**
 * Hook to load a custom template for the "classified" CPT from the plugin directory.
 * This function overrides the default template and loads the one located in the plugin.
 *
 * @param string $template The path to the template.
 * @return string The path to the custom template if it exists, or the original template.
 */
function classified_custom_template( $template ) {
	if ( is_singular( 'classified' ) ) {
		// Define the path to the custom template inside the plugin directory.
		$plugin_template = plugin_dir_path( __FILE__ ) . 'single-classified.php';

		// Check if the file exists, and if so, load it.
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}

	// If not a classified CPT, return the original template.
	return $template;
}
add_filter( 'template_include', 'classified_custom_template' ); // Hook to load the custom template for Classifieds.
