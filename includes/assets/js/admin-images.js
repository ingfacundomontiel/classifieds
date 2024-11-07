jQuery(document).ready(function($) {
    let file_frame;
    let imageIDsInput = $('#classified_images');
    let imagePreviewContainer = $('.classified-images-admin');

    // Handle the click event on the upload button
    $('#upload_images_button').on('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create a new media frame
        file_frame = wp.media({
            title: 'Seleccionar imágenes para Clasificado',
            button: {
                text: 'Actualizar Imágenes',
            },
            multiple: true // Set to true to allow multiple files to be selected
        });

        // When the media frame is opened, pre-select existing images
        file_frame.on('open', function() {
            let selectedImageIDs = imageIDsInput.val().split(',').filter(Boolean); // Get existing IDs
            let selection = file_frame.state().get('selection');

            selectedImageIDs.forEach(function(id) {
                let attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });
        });

        // When images are selected or deselected, grab the attachment IDs and update the view
        file_frame.on('select', function() {
            const attachments = file_frame.state().get('selection').toJSON();
            let imageIDs = [];

            // Clear the container first
            imagePreviewContainer.html('');

            // Loop through selected images and update the image preview and IDs
            attachments.forEach(function(attachment) {
                imageIDs.push(attachment.id); // Add the image ID

                // Generate image preview HTML
                const imageHtml = `
                    <div class="classified-image" data-id="${attachment.id}" style="display: inline-block; margin-right: 10px;">
                        <img src="${attachment.sizes.thumbnail.url}" style="max-width: 100px;" />
                    </div>`;
                imagePreviewContainer.append(imageHtml);
            });

            // Update the hidden input field with the selected image IDs
            imageIDsInput.val(imageIDs.join(','));
        });

        // Finally, open the media frame
        file_frame.open();
    });
});
