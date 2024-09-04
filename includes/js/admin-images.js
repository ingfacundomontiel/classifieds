jQuery(document).ready(function ($) {
    var mediaUploader;

    $('#upload_images_button').click(function (e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Images',
            button: {
                text: 'Add to images'
            },
            multiple: true
        });

        mediaUploader.on('select', function () {
            var selection = mediaUploader.state().get('selection');
            var imagesIds = [];
            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                imagesIds.push(attachment.id);
            });

            $('#classified_images').val(imagesIds.join(','));
        });

        mediaUploader.open();
    });
});
