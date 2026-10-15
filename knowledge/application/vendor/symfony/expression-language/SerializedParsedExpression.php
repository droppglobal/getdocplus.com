<?php



namespace Symfony\Component\ExpressionLanguage;

/**
 * Represents an already parsed expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SerializedParsedExpression extends ParsedExpression
{
    private $nodes;

    /**
     * @param string $expression An expression
     * @param string $nodes      The serialized nodes for the expression
     */
    public function __construct($expression, $nodes)
    {
        $this->expression = (string) $expression;
        $this->nodes = $nodes;
    }

    public function getNodes()
    {
        return unserialize($this->nodes);
    }
}
