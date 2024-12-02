<?php
/**
 * Functions to handle email notifications for the Classifieds plugin.
 *
 * @package Classifieds
 */

/**
 * Send an email notification to administrators.
 *
 * @param int $classified_id The ID of the Classified.
 * @return void
 */
function notify_admin_of_new_classified( $classified_id ) {
	$post = get_post( $classified_id );

	$to = array(
		'comunicpractica@gmail.com',
		'ingfacundomontiel@gmail.com',
		'info@ganaderiaynegocios.com',
	);

	$subject  = 'Nuevo Clasificado - Pendiente de moderación';
	$message  = 'Se ha enviado un nuevo Clasificado.' . "\n\n";
	$message .= 'Título: ' . $post->post_title . "\n";
	$message .= 'Correo electrónico del vendedor: ' . get_post_meta( $classified_id, '_classified_email', true ) . "\n\n";
	$message .= 'Para revisar y aprobar el Clasificado, visita el panel de administración de WordPress.' . "\n\n";

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	wp_mail( $to, $subject, $message, $headers );
}

/**
 * Send a confirmation email to the user who submitted the classified.
 *
 * @param int $classified_id The ID of the Classified.
 * @return void
 */
function notify_user_of_submission( $classified_id ) {
	$post       = get_post( $classified_id );
	$user_email = get_post_meta( $classified_id, '_classified_email', true );

	if ( ! is_email( $user_email ) ) {
		return;
	}

	$subject  = 'Confirmación de envío de Clasificado';
	$message  = '¡Gracias por enviar tu Clasificado!' . "\n\n";
	$message .= 'Título: ' . $post->post_title . "\n";
	$message .= 'Precio: ' . get_post_meta( $classified_id, '_classified_price', true ) . "\n";
	$message .= 'Localidad: ' . get_post_meta( $classified_id, '_classified_location', true ) . "\n\n";
	$message .= 'Tu Clasificado está pendiente de aprobación. Esto puede tardar hasta 48 horas.' . "\n\n";
	$message .= 'Para ver todos los Clasificados ingresando aquí: https://ganaderiaynegocios.com/clasificados/.' . "\n\n";

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	wp_mail( $user_email, $subject, $message, $headers );
}

/**
 * Notify the user when their Classified is published.
 *
 * @param string  $new_status The new status of the post.
 * @param string  $old_status The previous status of the post.
 * @param WP_Post $post The post object.
 */
function notify_user_when_classified_published( $new_status, $old_status, $post ) {
	// Check if the post type is 'classified' and the status changed to 'publish'.
	if ( 'classified' === $post->post_type && 'publish' === $new_status && 'publish' !== $old_status ) {
		// Get the user's email from the post meta.
		$user_email = get_post_meta( $post->ID, '_classified_email', true );

		if ( ! is_email( $user_email ) ) {
			return; // Exit if the email is not valid.
		}

		// Prepare the email.
		$subject  = 'Tu Clasificado ha sido publicado';
		$message  = '¡Buenas noticias!' . "\n\n";
		$message .= 'Tu Clasificado "' . $post->post_title . '" ha sido aprobado y publicado en nuestro sitio.' . "\n\n";
		$message .= 'Puedes verlo aquí: ' . get_permalink( $post->ID ) . "\n\n";
		$message .= 'Gracias por utilizar nuestro servicio.' . "\n\n";

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		// Send the email.
		wp_mail( $user_email, $subject, $message, $headers );
	}
}

// Hook into the status transition.
add_action( 'transition_post_status', 'notify_user_when_classified_published', 10, 3 );
