<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

/**
 * Dropdown-selection for referencing posts of a specific post-type to be used for a post-type's custom-field.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class DropdownForReferencedPost extends CustomField
{

    /**
     * Renders the input's markup.
     *
     * @param \WP_Post|\WP_Term|null $postOrTerm
     * @param string $fieldId
     * @param string|null $value
     *
     * @see https://github.com/laktek/really-simple-color-picker
     */
    function renderField($postOrTerm = null, string $fieldId, string $value = null): void
    {
        //  check if non-empty post given
        if (empty($postOrTerm) || !$postOrTerm instanceof \WP_Post) {
            //  currently no other type allowed than posts
            throw new \InvalidArgumentException('Currently no taxonomy support, use this field on custom post-types only.');
        }

        //  the actual input to be POSTed
        echo '<input type="text" style="display: none;" id="' . $fieldId . '" name="' . $fieldId . '" value="' . $value . '" />';

        //  helper input to fill actual input
        echo '<select id="dropdown-' . $fieldId . '" name="dropdown-' . $fieldId . '" size="1">';
        //  default option
        echo '<option value="">—</option>';

        //  load all posts of the referenced post-type
        query_posts([
            /** @see http://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters */
            'post_type' => $this->slug,
            /** @see http://codex.wordpress.org/Class_Reference/WP_Query#Pagination_Parameters */
            'nopaging' => true,
            /** @see http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters */
            'order' => 'ASC',
            'orderby' => 'ID',
        ]);

        //  split value by delimiter: each sub-string is an ID of the referenced posts
        $ids = explode(';', $value);

        //  ensure to have correct data-type --> get integer value
        foreach ($ids as $key => $id) {
            $ids[$key] = intval($id);
        }

        //  iterate all posts
        while (have_posts()) {
            the_post();
            global $post;

            //  grab values for the option's attributes
            $optionLabel = $post->post_title;
            $optionValue = $post->ID;

            //  check if current option-entry has to be selected by default
            $selected = in_array($optionValue, $ids) ? 'selected="selected"' : '';

            //  render the option
            echo '<option value="' . $optionValue . '" ' . $selected . '>' . $optionLabel . '</option>';
        }

        echo '</select>';

        //  reset post-data as recommended by official docs (http://codex.wordpress.org/Class_Reference/WP_Query#Usage)
        wp_reset_postdata();
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
        ?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var field_selector = '#<?php echo $fieldId; ?>';
				var dropdown_selector = '#dropdown-<?php echo $fieldId; ?>';

				jQuery(dropdown_selector).change(function (e) {
					var ids = [];

					//  iterate all selection options
					jQuery(dropdown_selector + ' option:selected').each(function () {
						//  get the post id for each selected entry
						var id = jQuery(this).val();
						//  and add it to the array
						ids.push(id);
					});

					//  numerically order the ids array
					ids.sort(function (a, b) {
						return a - b
					});

					//  get comma-separated list of ids
					ids_string = ids.join(';');
					//  update textfield with resulting string
					jQuery(field_selector).val(ids_string);
				});
			});
		</script><?php
    }
}
