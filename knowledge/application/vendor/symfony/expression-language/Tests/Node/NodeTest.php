<?php



namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Node\Node;
use Symfony\Component\ExpressionLanguage\Node\ConstantNode;

class NodeTest extends TestCase
{
    public function testToString()
    {
        $node = new Node(array(new ConstantNode('foo')));

        $this->assertEquals(<<<'EOF'
Node(
    ConstantNode(value: 'foo')
)
EOF
        , (string) $node);
    }

    public function testSerialization()
    {
        $node = new Node(array('foo' => 'bar'), array('bar' => 'foo'));

        $serializedNode = serialize($node);
        $unserializedNode = unserialize($serializedNode);

        $this->assertEquals($node, $unserializedNode);
    }
}
