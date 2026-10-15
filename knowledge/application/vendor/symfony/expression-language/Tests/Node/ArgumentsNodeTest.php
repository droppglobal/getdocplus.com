<?php



namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use Symfony\Component\ExpressionLanguage\Node\ArgumentsNode;

class ArgumentsNodeTest extends ArrayNodeTest
{
    public function getCompileData()
    {
        return array(
            array('"a", "b"', $this->getArrayNode()),
        );
    }

    public function getDumpData()
    {
        return array(
            array('"a", "b"', $this->getArrayNode()),
        );
    }

    protected function createArrayNode()
    {
        return new ArgumentsNode();
    }
}
