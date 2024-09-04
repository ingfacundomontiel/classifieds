<?php

function display_classified_form() {
	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['submit_classified'] ) ) {
		$nonce = $_POST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'submit_classified' ) ) {
			die( 'Security check failed' );
		}

		$classified_data = array(
			'post_title'   => sanitize_text_field( $_POST['classified_title'] ),
			'post_content' => sanitize_textarea_field( $_POST['classified_description'] ),
			'post_status'  => 'pending',
			// 'post_status'  => 'publish',
			'post_type'    => 'classified',
		);

		$classified_price    = isset( $_POST['classified_price'] ) ? floatval( $_POST['classified_price'] ) : 0;
		$classified_currency = sanitize_text_field( $_POST['classified_currency'] );

		// Verificar que el post se haya creado correctamente.
		$classified_id = wp_insert_post( $classified_data );

		if ( $classified_id ) {
            
			// Asignar la categoría al post recién creado.
            if ( isset( $_POST['classified_category'] ) && ! empty( $_POST['classified_category'] ) ) {
				$categories = array_map( 'intval', $_POST['classified_category'] );
				wp_set_post_terms( $classified_id, $categories, 'classified_category' );  // Asigna las categorías
			}

			// Guardar el precio del Clasificado como meta.
			update_post_meta( $classified_id, '_classified_price', $classified_price );

			// Guardar la moneda seleccionada como un custom field
			update_post_meta( $classified_id, '_classified_currency', $classified_currency );

			// Manejar las imágenes.
			if ( ! empty( $_FILES['classified_images']['name'][0] ) ) {
				$image_ids = array(); // Arreglo para almacenar los IDs de las imágenes subidas.

				// Limitar a 5 imágenes
				$image_count = count( $_FILES['classified_images']['name'] );
				if ( $image_count > 5 ) {
					$image_count = 5; // Limitar a máximo 5 imágenes.
				}

				for ( $i = 0; $i < $image_count; $i++ ) {
					// Subir cada imagen.
					$file = array(
						'name'     => $_FILES['classified_images']['name'][ $i ],
						'type'     => $_FILES['classified_images']['type'][ $i ],
						'tmp_name' => $_FILES['classified_images']['tmp_name'][ $i ],
						'error'    => $_FILES['classified_images']['error'][ $i ],
						'size'     => $_FILES['classified_images']['size'][ $i ],
					);

					// Subir la imagen usando wp_handle_upload.
					$upload_overrides = array( 'test_form' => false );
					$movefile         = wp_handle_upload( $file, $upload_overrides );

					if ( $movefile && ! isset( $movefile['error'] ) ) {
						// La imagen fue subida exitosamente, ahora la asociamos al post.
						$attachment = array(
							'post_mime_type' => $movefile['type'],
							'post_title'     => sanitize_file_name( $movefile['file'] ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						);

						$attachment_id = wp_insert_attachment( $attachment, $movefile['file'], $classified_id );

						// Generar los metadatos de la imagen.
						require_once ABSPATH . 'wp-admin/includes/image.php';
						$attach_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
						wp_update_attachment_metadata( $attachment_id, $attach_data );

						// Añadir el ID de la imagen al array de imágenes.
						$image_ids[] = $attachment_id;
					}
				}

				// Guardar los IDs de las imágenes como meta en el post.
				if ( ! empty( $image_ids ) ) {
					update_post_meta( $classified_id, '_classified_images', $image_ids );
				}
			}

			echo '<p>¡Tu Clasificado se ha creado correctamente!</p>';
		} else {
			echo '<p>Hubo un error procesando tu Clasificado. Por favor, refresca la página e intentalo nuevamente.</p>';
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

		<input type="submit" name="submit_classified" value="Enviar Clasificado">
	</form>

	<!-- <script type="text/javascript">
		document.getElementById('classifiedForm').addEventListener('submit', function(event) {
			event.preventDefault(); // Evita el envío del formulario

			// Capturar datos del formulario
			var formData = new FormData(event.target);
			var formObject = {};
			formData.forEach((value, key) => {
				formObject[key] = value
			});

			// Mostrar los datos capturados en la consola
			console.log('Form Data:', formObject);

			// Mostrar los datos capturados en el HTML (opcional)
			var resultDiv = document.createElement('div');
			resultDiv.innerHTML = '<pre>' + JSON.stringify(formObject, null, 4) + '</pre>';
			document.body.appendChild(resultDiv);
		});
	</script> -->

	<?php
	return ob_get_clean();
}

add_shortcode( 'classified_form', 'display_classified_form' );
