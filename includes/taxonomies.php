<?php

// Registrar la taxonomía personalizada para los Clasificados
function register_classified_category_taxonomy() {
    // Labels para la taxonomía
    $labels = array(
        'name'              => _x( 'Categorías', 'taxonomy general name' ),
        'singular_name'     => _x( 'Categoría', 'taxonomy singular name' ),
        'search_items'      => __( 'Buscar Categorías' ),
        'all_items'         => __( 'Todas las Categorías' ),
        'parent_item'       => __( 'Categoría Padre' ),
        'parent_item_colon' => __( 'Categoría Padre:' ),
        'edit_item'         => __( 'Editar Categoría' ),
        'update_item'       => __( 'Actualizar Categoría' ),
        'add_new_item'      => __( 'Agregar Nueva Categoría' ),
        'new_item_name'     => __( 'Nuevo Nombre de Categoría' ),
        'menu_name'         => __( 'Categorías' ),
    );

    // Registrar la taxonomía con los argumentos
    register_taxonomy('classified_category', array('classified'), array(
        'hierarchical'      => true, // true para que se comporte como una categoría
        'labels'            => $labels,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'classified-category' ), 
    ));
}

add_action('init', 'register_classified_category_taxonomy');

function classified_add_taxonomy_metaboxes() {
    remove_meta_box( 'classified_categorydiv', 'classified', 'side' );
    add_meta_box( 'classified_categorydiv', 'Categorías', 'post_categories_meta_box', 'classified', 'side', 'default', array( 'taxonomy' => 'classified_category' ) );
}

add_action( 'add_meta_boxes', 'classified_add_taxonomy_metaboxes' );