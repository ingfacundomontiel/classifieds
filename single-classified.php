<?php
/**
 * The template for displaying a single Classified post.
 *
 * @package classifieds
 */

get_header();
?>
<main id="primary" class="site-main single-page single-classified-content">

	<section class="block the-content">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-9 content-col">

					<?php
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
									<strong>Categorías: </strong>
									<?php
									display_post_terms( get_the_ID(), 'classified_category' );
									?>
								</div>


							<?php

							// Include the inquiry form from an external file.
							require plugin_dir_path( __FILE__ ) . 'includes/single/inquiry-form.php';

						endwhile;
					endif;
					?>

							</div>
				</div>
			</div>
	</section>

</main><!-- #main -->

<?php
get_footer();
