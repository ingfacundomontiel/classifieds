<?php
/**
 * The template for displaying a single Classified post.
 *
 * @package classifieds
 */

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

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

					<p><strong>Precio: </strong> $<?php echo ( 'USD' === $currency ) ? 'USD$ ' : 'ARS$ ', esc_html( $price ); ?> </p>

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
				<strong>Categorías: </strong><?php echo esc_html( get_the_category_list( ', ' ) ); ?>
			</div>

		<?php

		// Include the inquiry form from an external file.
		require plugin_dir_path( __FILE__ ) . 'includes/single/inquiry-form.php';

	endwhile;
endif;
