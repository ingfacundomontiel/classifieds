<?php
/**
 * Plugin Name: Classifieds Plugin
 * Description: A plugin to add classifieds functionality to your WordPress site.
 * Version: 1.1
 * Author: Facundo Montiel - Faca
 *
 * @package classifieds
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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

// Include necessary files.
require_once plugin_dir_path( __FILE__ ) . 'includes/post-types.php'; // Include the file that registers post types.
require_once plugin_dir_path( __FILE__ ) . 'includes/taxonomies.php'; // Include the file that registers taxonomies.
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php'; // Include the file that registers shortcodes.


// Include Functions.

require_once plugin_dir_path( __FILE__ ) . 'functions/enqueue-assets.php'; // Include the file that enqueues the assets.
require_once plugin_dir_path( __FILE__ ) . 'functions/custom-template.php'; // Include the file that loads the custom template.
require_once plugin_dir_path( __FILE__ ) . 'functions/print-classified-categories.php'; // Include the file that prints the categories.
require_once plugin_dir_path( __FILE__ ) . 'functions/custom-excerpt.php'; // Include the file that customizes excerpts.
require_once plugin_dir_path( __FILE__ ) . 'functions/featured-image.php'; // Include the file that sets the featured image from the gallery.