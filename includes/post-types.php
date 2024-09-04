<?php
function create_classifieds_post_type() {
	$labels = array(
		'name'               => _x( 'Clasificados', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Clasificado', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Clasificados', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Clasificado', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Agregar Nuevo', 'classified', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Agregar Nuevo Clasificado', 'your-plugin-textdomain' ),
		'new_item'           => __( 'Nuevo Clasificado', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Editar Clasificado', 'your-plugin-textdomain' ),
		'view_item'          => __( 'Ver Clasificado', 'your-plugin-textdomain' ),
		'all_items'          => __( 'Todos los Clasificados', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Buscar Clasificados', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Clasificado Padre:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No se encontraron clasificados.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No se encontraron clasificados en la papelera.', 'your-plugin-textdomain' ),
	);

	$args = array(
		'labels'       => $labels,
		'public'       => true,
		'has_archive'  => true,
		'supports'     => array( 'title', 'editor', 'comments', 'thumbnail' ),
		'taxonomies'   => array( 'classified_category' ),
		'rewrite'      => array( 'slug' => 'classifieds' ),
		'show_in_rest' => true,
	);

	register_post_type( 'classified', $args );
}
add_action( 'init', 'create_classifieds_post_type' );


// Add custom fields to Classifieds

require plugin_dir_path( __FILE__ ) . 'meta-boxes/price.php';

require plugin_dir_path( __FILE__ ) . 'meta-boxes/images.php';

require plugin_dir_path( __FILE__ ) . 'meta-boxes/currency.php';