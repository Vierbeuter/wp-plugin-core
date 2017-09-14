<?php

namespace Vierbeuter\WordPress\Feature\Traits;

/**
 * The HasAdminNoticeSupport trait provides methods for adding messages (notices, warnings, errors, success messages) to
 * WP admin panel.
 *
 * @package Vierbeuter\WordPress\Feature\Traits
 */
trait HasAdminNoticeSupport
{

    /**
     * Hooks into the "admin_notices" action hook to show messages to the logged-in WP-user.
     *
     * Expected parameter is a callable of the method creating the message (which is creating and printing the markup).
     * For ease of use just implement the method in this feature class (unless you want to have a messages service or
     * stuff like that.
     *
     * Example usage:
     * <code>
     * $this->addMessage([$this, 'myAwesomeSuccessMessage']);
     * </code>
     *
     * The myAwesomeSuccessMessage() method might be implemented as follows to print a "Success." message.
     * <code>
     * public function myAwesomeSuccessMessage(): void {
     *    echo $this->getMessageMarkupSuccess('Success.');
     * }
     * </code>
     *
     * @param callable $printMessage
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupSuccess()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupInfo()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupWarning()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupError()
     * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
     */
    protected function addMessage(callable $printMessage): void
    {
        add_action('admin_notices', $printMessage);
    }

    /**
     * Returns the markup for confirmation or success messages.
     *
     * @param string $message
     * @param bool $dismissible
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::addMessage()
     */
    protected function getMessageMarkupSuccess(string $message, bool $dismissible = false): string
    {
        return static::getMessageMarkup($message, 'success', $dismissible);
    }

    /**
     * Returns the markup for standard notices or informational messages.
     *
     * @param string $message
     * @param bool $dismissible
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::addMessage()
     */
    protected function getMessageMarkupInfo(string $message, bool $dismissible = false): string
    {
        return static::getMessageMarkup($message, 'info', $dismissible);
    }

    /**
     * Returns the markup for warnings.
     *
     * @param string $message
     * @param bool $dismissible
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::addMessage()
     */
    protected function getMessageMarkupWarning(string $message, bool $dismissible = false): string
    {
        return static::getMessageMarkup($message, 'warning', $dismissible);
    }

    /**
     * Returns the markup for error messages.
     *
     * @param string $message
     * @param bool $dismissible
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::addMessage()
     */
    protected function getMessageMarkupError(string $message, bool $dismissible = false): string
    {
        return static::getMessageMarkup($message, 'error', $dismissible);
    }

    /**
     * Returns the markup for messages of given type.
     *
     * @param string $message
     * @param string $type
     * @param bool $dismissible
     *
     * @return string
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::addMessage()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupSuccess()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupInfo()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupWarning()
     * @see \Vierbeuter\WordPress\Feature\Feature::getMessageMarkupError()
     */
    private function getMessageMarkup(string $message, string $type, bool $dismissible): string
    {
        /** @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices */
        $class = 'notice notice-' . $type . ($dismissible ? ' is-dismissible' : '');

        return '<div class="' . $class . '"><p>' . $message . '</p></div>';
    }
}
