<?php

function display_classifieds_list( $atts ) {
	$args  = array(
		'post_type'      => 'classified',
		'posts_per_page' => -1,
	);
	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		echo '<div class="classified-list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$price     = get_post_meta( get_the_ID(), '_classified_price', true );
			$currency  = get_post_meta( get_the_ID(), '_classified_currency', true );
			$image_ids = get_post_meta( get_the_ID(), '_classified_images', true );
			?>
			<div class="classified">
				<h2>
					<?php
					the_title();
					?>
				</h2>
				<div class="content">
					<?php the_content(); ?>
				</div>
				<?php
				if ( ! empty( $price ) ) {
					?>
					<div class="price">

						<p><strong>Precio: </strong> $<?php echo ( $currency === 'USD' ) ? 'USD$ ' : 'ARS$ ', esc_html( $price ); ?> </p>

					</div>
					<?php
				}
				if ( ! empty( $image_ids ) ) {
					echo '<div class="classified-gallery">';
					foreach ( $image_ids as $image_id ) {
						echo wp_get_attachment_image( $image_id, 'medium' );
					}
					echo '</div>';
				}
				?>
				<div class="categories">
					<strong>Categorías: </strong><?php echo get_the_category_list( ', ' ); ?>
				</div>
				<div>
					<?php comments_template(); ?>
				</div>
			</div>
			<?php
		}
		echo '</div>';
	} else {
		echo 'No classifieds found.';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'classifieds_list', 'display_classifieds_list' );
