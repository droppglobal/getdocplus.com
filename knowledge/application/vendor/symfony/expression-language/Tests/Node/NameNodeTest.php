<?php



namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use Symfony\Component\ExpressionLanguage\Node\NameNode;

class NameNodeTest extends AbstractNodeTest
{
    public function getEvaluateData()
    {
        return array(
            array('bar', new NameNode('foo'), array('foo' => 'bar')),
        );
    }

    public function getCompileData()
    {
        return array(
            array('$foo', new NameNode('foo')),
        );
    }

    public function getDumpData()
    {
        return array(
            array('foo', new NameNode('foo')),
        );
    }
}
