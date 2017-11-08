<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Text-field and upload-file-field to be used for a post-type's custom-field containing an image URL or an image
 * attachment.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class ImageField extends CustomField
{

    /**
     * flag to determine if an attachment ID should be used instead of the image URL
     *
     * @var bool
     */
    protected $useAttachmentId;

    /**
     * ImageField constructor.
     *
     * @param string $slug
     * @param string $label
     * @param string|null $description
     * @param bool $use_attachment_id
     */
    function __construct(string $slug, string $label, string $description = null, bool $use_attachment_id = true)
    {
        $defaultDescription = $this->vbTranslate('You can either insert an image-URL, upload a new image or choose an image from media library.');
        parent::__construct($slug, $label, empty($description) ? $defaultDescription : $description);

        $this->useAttachmentId = $use_attachment_id;
    }

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term|null $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    protected function renderField($postOrTerm = null, string $fieldId, string $value = null): void
    {
        //  define field-ids for preview and upload-button
        $previewId = $fieldId . '_preview';
        $buttonId = $fieldId . '_upload';

        //  given $value is the image
        //  (it's either numeric which is the attachment id or it's alphanumeric which is the image-URL)
        $image = $value;

        //  if ID is given
        if (is_numeric($image)) {
            //  load URL from database
            $image = wp_get_attachment_thumb_url($image);
        }
        //  else it's already an image-URL

        echo '<img id="' . $previewId . '" src="' . $image . '" style="max-width: 500px; max-height: 150px; display: none;" alt="' . $this->vbTranslate('no image') . '" /><div style="display: block;"></div>';
        echo '<input type="text" id="' . $fieldId . '" name="' . $fieldId . '" value="' . ($this->useAttachmentId ? $value : $image) . '" />';
        echo '<input id="' . $buttonId . '" class="button" type="button" value="' . $this->vbTranslate('Media library / Upload image') . '" />';
    }

    /**
     * Renders additional markup after the input to add Javascript snippets for instance or any other stuff like that.
     *
     * @param \WP_Post|\WP_Term|null $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    protected function renderAnythingAfterField($postOrTerm = null, string $fieldId, string $value = null): void
    {
        //  activate JS-libs and -APIs for the media library to make the following JS snippet work which uses the media-uploader
        wp_enqueue_media();

        //  define field-ids for preview and upload-button
        $previewId = $fieldId . '_preview';
        $buttonId = $fieldId . '_upload';

        //  JS for using the media library
        ?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var custom_uploader;

				jQuery('#<?php echo $buttonId; ?>').click(function (e) {
					e.preventDefault();

					//  if the uploader object has already been created, reopen the dialog
					if (custom_uploader) {
						custom_uploader.open();
						return;
					}

					//  extend the wp.media object
					custom_uploader = wp.media.frames.file_frame = wp.media({
						title: '<?php echo $this->vbTranslate('Choose or upload image'); ?>',
						button: {
							text: '<?php echo $this->vbTranslate('Use this image'); ?>'
						},
						multiple: false
					});

					//  when a file is selected, grab the URL and set it as the text field's value
					custom_uploader.on('select', function () {
						attachment = custom_uploader.state().get('selection').first().toJSON();
						jQuery('#<?php echo $fieldId; ?>').val(attachment.<?php echo $this->useAttachmentId ? 'id' : 'url'; ?>);
						jQuery('#<?php echo $previewId; ?>').attr('src', attachment.sizes.thumbnail.url).show('slow');
					});

					//  open the uploader dialog
					custom_uploader.open();
				});

				//  if field not empty
				if (jQuery('#<?php echo $fieldId; ?>').val().replace(/\s/g, '') != '') {
					//  show preview image
					jQuery('#<?php echo $previewId; ?>').attr('src', jQuery('#<?php echo $fieldId; ?>').val()).show('slow');
				}
			});
		</script><?php
    }
}
