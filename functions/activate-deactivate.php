<?php
/**
 * Functions to activate and deactivate the Classifieds Plugin.
 *
 * @package classifieds
 */

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
