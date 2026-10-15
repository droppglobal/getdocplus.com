<?php



namespace Symfony\Component\ExpressionLanguage\Node;

use Symfony\Component\ExpressionLanguage\Compiler;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
class NameNode extends Node
{
    public function __construct($name)
    {
        parent::__construct(
            array(),
            array('name' => $name)
        );
    }

    public function compile(Compiler $compiler)
    {
        $compiler->raw('$'.$this->attributes['name']);
    }

    public function evaluate($functions, $values)
    {
        return $values[$this->attributes['name']];
    }

    public function toArray()
    {
        return array($this->attributes['name']);
    }
}
