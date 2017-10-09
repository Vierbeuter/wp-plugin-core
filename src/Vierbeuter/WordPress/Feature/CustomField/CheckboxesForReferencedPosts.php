<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Checkbox-list for referencing multiple posts of a specific post-type to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class CheckboxesForReferencedPosts extends CustomField
{

    /**
     * @var int
     */
    protected $labelWidthPx;

    /**
     * CheckboxesForReferencedPosts constructor.
     *
     * @param string $postTypeSlug
     * @param string $label
     * @param string|null $description
     * @param int $labelWidthPx (optional) width of the checkbox labels in pixels; default: 200
     */
    function __construct(string $postTypeSlug, string $label, string $description = null, $labelWidthPx = 200)
    {
        parent::__construct($postTypeSlug, $label, $description);

        $this->labelWidthPx = $labelWidthPx;
    }

    /**
     * Returns the given post's post-type slug.
     *
     * @param \WP_Post $post
     *
     * @return string
     */
    protected function getReferencedPostTypeSlug(\WP_Post $post)
    {
        //  method may be overridden
        return $this->slug;
    }

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     */
    function renderField($postOrTerm, string $fieldId, string $value = null): void
    {
        //	check if post given
        if (!$postOrTerm instanceof \WP_Post) {
            //	currently no other type allowed than posts
            throw new \InvalidArgumentException('Currently no taxonomy support, use this field on custom post-types only.');
        }

        //	the actual input to be POSTed
        echo '<input type="text" style="display: none;" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $value . '" />';

        //	load all posts of the referenced post-type
        query_posts([
            /** @see http://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters */
            'post_type' => $this->getReferencedPostTypeSlug($postOrTerm),
            /** @see http://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters */
            'nopaging' => true,
            /** @see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters */
            'order' => 'ASC',
            'orderby' => 'menu_order title date ID',
        ]);

        //	split value by delimiter: each sub-string is an ID of the referenced posts
        $ids = explode(';', $value);

        //	ensure to have correct data-type --> get integer value
        foreach ($ids as $key => $id) {
            $ids[$key] = intval($id);
        }

        //	iterate all posts
        while (have_posts()) {
            the_post();
            global $post;

            //  grab values for the checkbox' attributes
            $checkboxId = $fieldId . '_' . $post->ID;
            $checkboxLabel = $post->post_title;
            $checkboxValue = $post->ID;

            //  check if current checkbox has to be selected by default
            $checked = in_array($checkboxValue, $ids) ? 'checked="checked"' : '';

            //  render checkbox markup (as helper input to fill actual input from above)
            echo '<div style="display: inline-block; float: left; width: ' . $this->labelWidthPx . 'px; overflow: hidden;">';
            echo '<input type="checkbox" class="' . $fieldId . ' checkbox-' . $this->slug . '" id="' . $checkboxId . '" name="' . $checkboxId . '" value="' . $checkboxValue . '"' . $checked . ' />&nbsp;';
            echo '<label for="' . $checkboxId . '" style="white-space: nowrap;">' . $checkboxLabel . '</label>';
            echo '</div>';
        }

        //	reset post-data as recommended by official docs (http://codex.wordpress.org/Class_Reference/WP_Query#Usage)
        wp_reset_postdata();

        ?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var field_selector = '#<?php echo $fieldId; ?>';

				jQuery('input.<?php echo $fieldId; ?>.checkbox-<?php echo $this->slug; ?>').change(function (e) {
					//  get value from textfield
					var ids_string = jQuery(field_selector).val();
					//  trim whitespaces
					var ids_string_trimmed = ids_string.replace(/\s/g, '');
					//  get all ids as array
					var ids = ids_string_trimmed.split(';');
					//  get value of changed checkbox, that is the post's id
					var id = jQuery(this).val();

					//  get current position of id in ids array
					var id_position = ids.indexOf(id);

					//  if id not in array
					if (id_position < 0) {
						//  add it
						ids.push(id);
					}
					//  if id is in array
					else {
						//  remove it
						ids.splice(id_position, 1);
					}

					//  numerically order the ids array
					ids.sort(function (a, b) {
						return a - b
					});

					//  get comma-separated list of ids again
					ids_string = ids.join(';');
					//  update textfield with that string
					jQuery(field_selector).val(ids_string);
				});
			});
		</script>
        <?php
    }
}
