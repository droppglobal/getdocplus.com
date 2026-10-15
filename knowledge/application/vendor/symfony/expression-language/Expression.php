<?php



namespace Symfony\Component\ExpressionLanguage;

/**
 * Represents an expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Expression
{
    protected $expression;

    /**
     * @param string $expression An expression
     */
    public function __construct($expression)
    {
        $this->expression = (string) $expression;
    }

    /**
     * Gets the expression.
     *
     * @return string The expression
     */
    public function __toString()
    {
        return $this->expression;
    }
}
