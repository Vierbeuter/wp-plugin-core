<?php

namespace Vierbeuter\WordPress\Feature\Traits;

/**
 * The HasWpHookSupport trait provides
 *
 * @package Vierbeuter\WordPress\Feature\Traits
 */
trait HasWpHookSupport
{

    /**
     * Intializes the WordPress hooks as defined in the sub-class.
     *
     * Override getActionHooks() and getFilterHooks() methods to define these hooks.
     *
     * @see \Vierbeuter\WordPress\Feature\Feature::getActionHooks()
     * @see \Vierbeuter\WordPress\Feature\Feature::getFilterHooks()
     */
    private function initWpHooks(): void
    {
        //  iterate the list of action-hooks as defined in sub-class
        foreach ($this->getActionHooks() as $hookName => $hookInfo) {
            //  set defaults for hook function
            $methodName = is_string($hookInfo) ? $hookInfo : (is_string($hookName) ? $hookName : null);
            $priority = 10;
            $arguments = 1;

            //  either it's an array or a string (for latter one defaults from above will be used)
            if (is_array($hookInfo)) {
                //  override defaults with values from $hookInfo array
                $methodName = empty($hookInfo['method']) ? $methodName : $hookInfo['method'];
                $priority = empty($hookInfo['prio']) ? $priority : $hookInfo['prio'];
                $arguments = empty($hookInfo['args']) ? $arguments : $hookInfo['args'];
            }

            //  use method name for hook if no name given
            if (is_numeric($hookName)) {
                $hookName = $methodName;
            }

            //  if still empty then it's just missing
            if (empty($methodName)) {
                throw new \Exception('Hook not properly defined in ' . get_called_class() . '::getActionHooks(): missing hook and/or method name for ' . $hookName . ' => array("prio" => ' . $priority . ', "args" => ' . $arguments . ')');
            }

            //  correct method name in case of hook has hyphens
            $methodName = str_replace('-', '_', $methodName);

            //  check first if hook-method exists before defining the callable
            if (!method_exists($this, $methodName)) {
                throw new \Exception('Hook not properly defined in ' . get_called_class() . '::getActionHooks(): missing hook implementation for "' . $hookName . '". Please add method ' . $methodName . '() or delete hook "' . $hookName . '".');
            }

            //  define callable for the action hook --> associate each hook with method of same name (within this class)
            $callable = [$this, $methodName];

            //  register the callable for current action-hook
            add_action($hookName, $callable, $priority, $arguments);
        }

        //  iterate the list of filter-hooks as defined in sub-class
        foreach ($this->getFilterHooks() as $hookName => $hookInfo) {
            //  set defaults for hook function
            $methodName = is_string($hookInfo) ? $hookInfo : (is_string($hookName) ? $hookName : null);
            $priority = 10;
            $arguments = 1;

            //  either it's an array or a string (for latter one defaults from above will be used)
            if (is_array($hookInfo)) {
                //  Defaults ggf. überschreiben mit neuen Werten aus dem $hookInfo-Array
                $methodName = empty($hookInfo['method']) ? $methodName : $hookInfo['method'];
                $priority = empty($hookInfo['prio']) ? $priority : $hookInfo['prio'];
                $arguments = empty($hookInfo['args']) ? $arguments : $hookInfo['args'];
            }

            //  use method name for hook if no name given
            if (is_numeric($hookName)) {
                $hookName = $methodName;
            }

            //  if still empty then it's just missing
            if (empty($methodName)) {
                throw new \Exception('Hook not properly defined in ' . get_called_class() . '::getFilterHooks(): missing hook and/or method name for ' . $hookName . ' => array("prio" => ' . $priority . ', "args" => ' . $arguments . ')');
            }

            //  correct method name in case of hook has hyphens
            $methodName = str_replace('-', '_', $methodName);

            //  check first if hook-method exists before defining the callable
            if (!method_exists($this, $methodName)) {
                throw new \Exception('Hook not properly defined in ' . get_called_class() . '::getFilterHooks(): missing hook implementation for "' . $hookName . '". Please add method ' . $methodName . '() or delete hook "' . $hookName . '".');
            }

            //  define callable for the action hook --> associate each hook with method of same name (within this class)
            $callable = [$this, $methodName];

            //  register the callable for current filter-hook
            add_filter($hookName, $callable, $priority, $arguments);
        }
    }

    /**
     * Returns a list of actions to be hooked into by this class. For each hook there <strong>must</strong> be defined a
     * public method with the same name as the hook (unless the hook's name consists of hyphens "-", for the appropriate
     * method name underscores "_" have to be used).
     *
     * Valid entries of the returned array are single strings, key-value-pairs and arrays. See comments in the method's
     * default implementation.
     *
     * @return string[]|array
     */
    protected function getActionHooks(): array
    {
        return [
            /*
            //  key can be omitted, just a simple string is enough
            'name_of_hook_and_method',

            //  key-value-pair consisting of the hook's name and the method
            'name_of_hook' => 'name_of_method',

            //  array-notation with full information
            'name_of_hook' => array(
                'method' => 'name_of_method',
                'prio' => 10,	//  priority is optional
                'args' => 1,	//  arguments is optional
            ),

            //  array-notation with leaving out optional keys and values
            'name_of_hook' => array(
                'method' => 'name_of_method',
            ),

            //  "method" key also optional as long as the array itself has a key
            'name_of_hook_and_method' => array(
                'prio' => 10,	//  priority is optional
                'args' => 1,	//  arguments is optional
            ),

            //  key of the array itself is optional as long as there is a corresponding entry for "method" key
            array(
                'method' => 'name_of_hook_and_method',
                'prio' => 10,	//  priority is optional
                'args' => 1,	//  arguments is optional
            ),
            */
        ];
    }

    /**
     * Returns a list of actions to be hooked into by this class. For each hook there <strong>must</strong> be defined a
     * public method with the same name as the hook (unless the hook's name consists of hyphens "-", for the appropriate
     * method name underscores "_" have to be used).
     *
     * Valid entries of the returned array are single strings, key-value-pairs and arrays. See comments in the method's
     * default implementation.
     *
     * @return string[]|array
     */
    protected function getFilterHooks(): array
    {
        return [
            /*
            //  key can be omitted, just a simple string is enough
            'name_of_hook_and_method',

            //  key-value-pair consisting of the hook's name and the method
            'name_of_hook' => 'name_of_method',

            //  array-notation with full information
            'name_of_hook' => array(
                'method' => 'name_of_method',
                'prio' => 10,	//  priority ist optional
                'args' => 1,	//  arguments ist optional
            ),

            //  array-notation with leaving out optional keys and values
            'name_of_hook' => array(
                'method' => 'name_of_method',
            ),

            //  "method" key also optional as long as the array itself has a key
            'name_of_hook_and_method' => array(
                'prio' => 10,	//  priority ist optional
                'args' => 1,	//  arguments ist optional
            ),

            //  key of the array itself is optional as long as there is a corresponding entry for "method" key
            array(
                'method' => 'name_of_hook_and_method',
                'prio' => 10,	//  priority ist optional
                'args' => 1,	//  arguments ist optional
            ),
            */
        ];
    }
}
