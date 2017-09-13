<?php

namespace Vierbeuter\WordPress\Feature\CustomField;

use Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType;

/**
 * A FieldGroup groups custom-fields.
 *
 * Will be used to render the sub-forms on a post-type's edit page in the admin-panel.
 *
 * @package Vierbeuter\WordPress\Feature\CustomField
 */
class FieldGroup
{

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var CustomField[]
     */
    protected $fields;

    /**
     * @var string
     */
    protected $nonceAction;

    /**
     * @var string
     */
    protected $description;

    /**
     * FieldGroup constructor.
     *
     * @param string $slug slug of the field-group
     * @param string $label label of the field-group
     * @param \Vierbeuter\WordPress\Feature\CustomField\CustomField[] $fields list of custom-fields
     * @param string $priority priority within the position (see given $context); valid values are 'high', 'core',
     *     'default' and 'low'; default='default'
     * @param string $context postion of thew field-group in admin-panel; valid values are 'advanced', 'normal' and
     *     'side'; default='normal'
     * @param string|null $description
     */
    function __construct(
        string $slug,
        string $label,
        array $fields = [],
        string $priority = 'default',
        string $context = 'normal',
        string $description = null
    ) {
        $this->slug = $slug;
        $this->label = $label;
        $this->fields = $fields;
        $this->priority = $priority;
        $this->context = $context;
        $this->nonceAction = md5($slug);
        $this->description = $description;
    }

    /**
     * Returns the slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Returns the priority.
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Returns the context.
     *
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * Returns the fields.
     *
     * @return \Vierbeuter\WordPress\Feature\CustomField\CustomField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Adds the given custom-field.
     *
     * @param CustomField $field
     */
    public function addField(CustomField $field): void
    {
        $this->fields[] = $field;
    }

    /**
     * Returns the "nonce"-action.
     *
     * This is gonna be used for security reason.
     *
     * @return string
     *
     * @see wp_nonce_field()
     * @see wp_verify_nonce()
     * @see http://codex.wordpress.org/WordPress_Nonces
     */
    public function getNonceAction(): string
    {
        return $this->nonceAction;
    }

    /**
     * Returns the "nonce" name.
     *
     * This is gonna be used for security reason.
     *
     * @param CustomField $field
     *
     * @return string
     *
     * @see wp_nonce_field()
     * @see wp_verify_nonce()
     * @see http://codex.wordpress.org/WordPress_Nonces
     */
    public function getNonceName(CustomField $field): string
    {
        return md5($this->getSlug() . $field->getSlug());
    }

    /**
     * Registers this field-group to the given post-type. The group will be added as meta-box.
     *
     * To define the callable for rendering the meta-box the render() method of FieldGroup class will be used.
     *
     * @param CustomPostType $postType
     *
     * @see add_meta_box()
     * @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::render()
     */
    public function register(CustomPostType $postType): void
    {
        add_meta_box($this->getSlug(), $this->getLabel(), [$this, 'render'], $postType->getSlug(),
            $this->getContext(), $this->getPriority(), $this->getFields());
    }

    /**
     * Renders the field-group.
     *
     * Is referenced as callable in the register() method.
     *
     * @param \WP_Post $post
     * @param array $args
     *
     * @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::register()
     */
    public function render(\WP_Post $post, array $args): void
    {
        echo '<table class="form-table">';

        //  render description if exists
        if (!empty($this->description)) {
            echo '<tr valign="top"><td colspan="2"><p>';
            echo $this->description;
            echo '</p></td></tr>';
        }

        //  iterate all custom-fields
        foreach ($this->getFields() as $field) {
            $fieldId = $this->getFieldId($field);
            $dbMetaKey = $this->getDbMetaKey($post->post_type, $fieldId);

            //  add "nonce" field for protection against crackers etc., see also funciton doc of wp_nonce_field()
            //  attention: whatever is passed to wp_nonce_field() has also be passed to wp_verify_nonce() for validation
            //  --> see implementation of save() method below
            /** @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::save() */
            wp_nonce_field($this->getNonceAction(), $this->getNonceName($field));

            //  Wert des Felds aus der DB lesen
            $value = get_post_meta($post->ID, $dbMetaKey, true);

            //  Markup für das Custom-Field ausgeben
            echo '<tr valign="top">';
            $field->render($post, $fieldId, $value);
            echo '</tr>';
        }

        echo '</table>';
    }

    /**
     * Saves the POSTed values of the field-group.
     *
     * @param string $postTypeSlug
     * @param int $postId
     */
    public function save(string $postTypeSlug, int $postId): void
    {
        //  iterate all custom-fields
        foreach ($this->getFields() as $field) {
            $fieldId = $this->getFieldId($field);
            $dbMetaKey = $this->getDbMetaKey($postTypeSlug, $fieldId);

            /*
             * We need to verify this came from our screen and with proper authorization,
             * because the save_post action can be triggered at other times.
             */

            //  check if our nonce is set
            if (!isset($_POST[$this->getNonceName($field)])) {
                continue;
            }

            //  verify that the nonce is valid
            //  attention: whatever is passed to wp_verify_nonce() has also be passed to wp_nonce_field() for validation
            //  --> see implementation of render() method above
            /** @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::render() */
            if (!wp_verify_nonce($_POST[$this->getNonceName($field)], $this->getNonceAction())) {
                continue;
            }

            //  if this is an autosave, our form has not been submitted, so we don't want to do anything
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                continue;
            }

            //  check the user's permissions
            $isPostTypePage = isset($_POST['post_type']) && 'page' == $_POST['post_type'];
            $currentUserCanEditPageOrPost = current_user_can($isPostTypePage ? 'edit_page' : 'edit_post', $postId);

            if (!$currentUserCanEditPageOrPost) {
                continue;
            }

            //  alright, it's safe for us to save the data now

            //  ensure that data is set
            if (!isset($_POST[$fieldId])) {
                continue;
            }

            $data = $_POST[$fieldId];

            //  check if data has to be sanitized
            if ($field->sanitizeValueOnSave()) {
                //  sanitize user input
                $data = sanitize_text_field($data);
            }

            //  update the meta field in the database
            update_post_meta($postId, $dbMetaKey, $data);
        }
    }

    /**
     * Returns the ID for the form-field.
     *
     * @param CustomField $field
     *
     * @return string
     */
    public function getFieldId(CustomField $field): string
    {
        //  concat slug of field-group with the actual field's slug
        return $this->getSlug() . '-' . $field->getSlug();
    }

    /**
     * Returns the database key for accessing the meta-value.
     *
     * @param string $postTypeSlug
     * @param string $fieldId
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\CustomField\FieldGroup::getFieldId()
     */
    protected function getDbMetaKey(string $postTypeSlug, string $fieldId): string
    {
        //  concat slug of post-type with ID of the meta-field
        return $postTypeSlug . '-' . $fieldId;
    }

    /**
     * Returns the database key for accessing the meta-value of given custom-field.
     *
     * The returned database key is more or less the "fully qualified" slug of the custom-field.
     *
     * @param \Vierbeuter\WordPress\Feature\CustomPostType\CustomPostType $post_type
     * @param CustomField $field
     *
     * @return string
     */
    public function getFieldDbMetaKey(CustomPostType $post_type, CustomField $field): string
    {
        $field_id = $this->getFieldId($field);
        $db_meta_key = $this->getDbMetaKey($post_type->getSlug(), $field_id);

        return $db_meta_key;
    }
}
