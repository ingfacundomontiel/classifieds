<?php
/**
 * Display a classified form for users to submit new classified posts.
 *
 * @package classifieds
 */

/**
 * Display a classified form for users to submit new classified posts.
 *
 * @return string The HTML of the classified form.
 */
function display_classified_form() {
	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['submit_classified'] ) ) {
		// Check if the nonce is set and valid.
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'submit_classified' ) ) {

			// Sanitize and process the classified data.
			$classified_data = array(
				'post_title'   => isset( $_POST['classified_title'] ) ? sanitize_text_field( wp_unslash( $_POST['classified_title'] ) ) : 'Error in Title',
				'post_content' => isset( $_POST['classified_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['classified_description'] ) ) : 'Error in Description',
				'post_status'  => 'pending',
				'post_type'    => 'classified',
			);

			$classified_price    = isset( $_POST['classified_price'] ) ? floatval( wp_unslash( $_POST['classified_price'] ) ) : 0;
			$classified_currency = sanitize_text_field( wp_unslash( $_POST['classified_currency'] ) );
			$classified_email    = sanitize_email( wp_unslash( $_POST['classified_email'] ) );

			// Insert the post.
			$classified_id = wp_insert_post( $classified_data );

			if ( $classified_id ) {

				// Assign the category.
				if ( isset( $_POST['classified_category'] ) && ! empty( $_POST['classified_category'] ) ) {
					$categories = array_map( 'intval', wp_unslash( $_POST['classified_category'] ) );
					wp_set_post_terms( $classified_id, $categories, 'classified_category' );
				}

				// Save custom fields.
				update_post_meta( $classified_id, '_classified_price', $classified_price );
				update_post_meta( $classified_id, '_classified_currency', $classified_currency );
				update_post_meta( $classified_id, '_classified_email', $classified_email );

				// Handle images.
				if ( ! empty( $_FILES['classified_images']['name'][0] ) ) {
					$image_ids   = array();
					$image_count = count( $_FILES['classified_images']['name'] );

					if ( $image_count > 5 ) {
						$image_count = 5;
					}

					for ( $i = 0; $i < $image_count; $i++ ) {
						$file = array(
							'name'     => $_FILES['classified_images']['name'][ $i ],
							'type'     => $_FILES['classified_images']['type'][ $i ],
							'tmp_name' => $_FILES['classified_images']['tmp_name'][ $i ],
							'error'    => $_FILES['classified_images']['error'][ $i ],
							'size'     => $_FILES['classified_images']['size'][ $i ],
						);

						$upload_overrides = array( 'test_form' => false );
						$movefile         = wp_handle_upload( $file, $upload_overrides );

						if ( $movefile && ! isset( $movefile['error'] ) ) {
							$attachment = array(
								'post_mime_type' => $movefile['type'],
								'post_title'     => sanitize_file_name( $movefile['file'] ),
								'post_content'   => '',
								'post_status'    => 'inherit',
							);

							$attachment_id = wp_insert_attachment( $attachment, $movefile['file'], $classified_id );
							require_once ABSPATH . 'wp-admin/includes/image.php';
							$attach_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
							wp_update_attachment_metadata( $attachment_id, $attach_data );
							$image_ids[] = $attachment_id;
						}
					}

					if ( ! empty( $image_ids ) ) {
						update_post_meta( $classified_id, '_classified_images', $image_ids );
					}
				}

				echo '<p>¡Tu Clasificado se ha creado correctamente!</p>';
			} else {
				echo '<p>Hubo un error procesando tu Clasificado. Por favor, refresca la página e intentalo nuevamente.</p>';
			}
		} else {
			// Nonce verification failed.
			echo '<p>Error de seguridad: El formulario no pudo ser procesado.</p>';
		}
	}

	$categories = get_terms(
		array(
			'taxonomy'    => 'classified_category',
			'hide_empty'  => false,
			'object_type' => array( 'classified' ),
		)
	);

	ob_start();
	?>

	<form id="classifiedForm" action="" method="post" enctype="multipart/form-data" class="classified-form">
		<?php wp_nonce_field( 'submit_classified' ); ?>
		<label for="classified_title">Título</label>
		<input type="text" id="classified_title" name="classified_title" required>

		<label for="classified_description">Descripción</label>
		<textarea id="classified_description" name="classified_description" required></textarea>

		<label for="classified_price">Precio</label>
		<input type="number" id="classified_price" name="classified_price" required>

		<label for="currency">Moneda:</label>
		<input type="radio" id="currency_ars" name="classified_currency" value="ARS" required />
		<label for="currency_ars">Pesos Argentinos</label>

		<input type="radio" id="currency_usd" name="classified_currency" value="USD" required />
		<label for="currency_usd">USD</label>

		<label for="classified_images">Imágenes (hasta 5):</label>
		<input type="file" id="classified_images" name="classified_images[]" multiple="multiple" accept="image/*" />

		<label for="classified_category">Categoria</label>
		<?php
		foreach ( $categories as $category ) {
			?>
			<input type="checkbox" name="classified_category[]" value="<?php echo esc_attr( $category->term_id ); ?>">
			<?php echo esc_html( $category->name ); ?>
		<?php } ?>

		<label for="classified_email">Correo electrónico</label>
		<input type="email" id="classified_email" name="classified_email" required />

		<input type="submit" name="submit_classified" value="Enviar Clasificado">
	</form>

	<?php
	return ob_get_clean();
}

add_shortcode( 'classified_form', 'display_classified_form' );
