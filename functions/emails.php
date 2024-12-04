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

	$subject  = 'Nuevo Clasificado: "' . $post->post_title . '" - Pendiente de Moderación';
	$message  = '<html><body>';
	$message .= '<h2>Nuevo Clasificado Pendiente de Moderación</h2>';
	$message .= '<p>Se ha enviado un nuevo Clasificado. A continuación, los detalles:</p>';
	$message .= '<ul>';
	$message .= '<li><strong>Título:</strong> ' . esc_html( $post->post_title ) . '</li>';
	$message .= '<li><strong>Correo electrónico del vendedor:</strong> ' . esc_html( get_post_meta( $classified_id, '_classified_email', true ) ) . '</li>';
	$message .= '</ul>';
	$message .= '<p>Para revisar y aprobar el Clasificado, visita el panel de administración de WordPress.</p>';
	$message .= '<img src="https://ganaderiaynegocios.com/wp-content/uploads/2021/03/ganaderia-y-negocios-logo-header.png" alt="Ganadería y Negocios" style="max-width: 200px;">';
	$message .= '</body></html>';

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
	);

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

	$subject  = 'Confirmación de envío de Clasificado: "' . $post->post_title . '"';
	$message  = '<p>¡Gracias por enviar tu Clasificado!</p>';
	$message .= '<p><strong>Título:</strong> ' . esc_html( $post->post_title ) . '</p>';
	$message .= '<p><strong>Precio:</strong> ' . esc_html( get_post_meta( $classified_id, '_classified_price', true ) ) . '</p>';
	$message .= '<p><strong>Localidad:</strong> ' . esc_html( get_post_meta( $classified_id, '_classified_location', true ) ) . '</p>';
	$message .= '<p>Tu Clasificado está pendiente de aprobación. Esto puede tardar hasta 48 horas.</p>';
	$message .= '<p>Para ver todos los Clasificados, haz clic <a href="https://ganaderiaynegocios.com/clasificados/">aquí</a>.</p>';
	$message .= '<img src="https://ganaderiaynegocios.com/wp-content/uploads/2021/03/ganaderia-y-negocios-logo-header.png" alt="Ganadería y Negocios" style="max-width: 200px;">';

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	wp_mail( $user_email, $subject, $message, $headers );
}

/**
 * Notify the user when their Classified is published.
 *
 * @param string  $new_status The new status of the post.
 * @param string  $old_status The previous status of the post.
 * @param WP_Post $post The post object.
 */
function notify_user_of_publication( $new_status, $old_status, $post ) {

	// Check if the post type is 'classified' and the status changed to 'publish'.
	if ( 'classified' === $post->post_type && 'publish' === $new_status && 'publish' !== $old_status ) {

		// Check if the user has already been notified.
		$user_notified = get_post_meta( $post->ID, '_user_notified_of_publication', true );

		if ( $user_notified ) {
			return; // Exit if the user has already been notified.
		}

		// Get the user's email from the post meta.
		$user_email = get_post_meta( $post->ID, '_classified_email', true );

		if ( ! is_email( $user_email ) ) {
			return; // Exit if the email is not valid.
		}

		// Prepare the email.
		$subject = 'Tu Clasificado "' . $post->post_title . '" ha sido publicado';
		$message = '
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="UTF-8">
				<title>Clasificado Publicado</title>
			</head>
			<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
				<h2 style="color: #2c3e50;">¡Buenas noticias!</h2>
				<p>Tu Clasificado "<strong>' . esc_html( $post->post_title ) . '</strong>" ha sido aprobado y publicado en nuestro sitio.</p>
				<p>Puedes verlo haciendo clic en el siguiente enlace:</p>
				<p>
					<a href="' . esc_url( get_permalink( $post->ID ) ) . '" 
					style="color: #4c9127; text-decoration: none; font-weight: bold; text-decoration: underline;">
					Ver Clasificado
					</a>
				</p>
				<p>Gracias por utilizar nuestro servicio.</p>
				<p style="font-size: 12px; color: #7f8c8d;">
					Si tienes alguna pregunta, no dudes en contactarnos.
				</p>
				<img src="https://ganaderiaynegocios.com/wp-content/uploads/2021/03/ganaderia-y-negocios-logo-header.png" alt="Ganadería y Negocios" style="max-width: 200px;">
			</body>
			</html>
		';

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		// Send the email.
		if ( wp_mail( $user_email, $subject, $message, $headers ) ) {
			// Update the user notification flag to true.
			update_post_meta( $post->ID, '_user_notified_of_publication', true );
		}
	}
}

// Hook into the status transition.
add_action( 'transition_post_status', 'notify_user_of_publication', 10, 3 );
