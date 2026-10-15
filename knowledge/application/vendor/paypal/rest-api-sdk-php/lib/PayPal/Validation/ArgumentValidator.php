<?php

namespace PayPal\Validation;

/**
 * Class ArgumentValidator
 *
 * @package PayPal\Validation
 */
class ArgumentValidator
{

    /**
     * Helper method for validating an argument that will be used by this API in any requests.
     *
     * @param $argument     mixed The object to be validated
     * @param $argumentName string|null The name of the argument.
     *                      This will be placed in the exception message for easy reference
     * @return bool
     */
    public static function validate($argument, $argumentName = null)
    {
        if ($argument === null) {
                        throw new \InvalidArgumentException("$argumentName cannot be null");
        } elseif (gettype($argument) == 'string' &&app_trim($argument) == '') {
                        throw new \InvalidArgumentException("$argumentName string cannot be empty");
        }
        return true;
    }
}
