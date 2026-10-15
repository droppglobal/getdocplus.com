<?php



namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use Symfony\Component\ExpressionLanguage\Node\ConditionalNode;
use Symfony\Component\ExpressionLanguage\Node\ConstantNode;

class ConditionalNodeTest extends AbstractNodeTest
{
    public function getEvaluateData()
    {
        return array(
            array(1, new ConditionalNode(new ConstantNode(true), new ConstantNode(1), new ConstantNode(2))),
            array(2, new ConditionalNode(new ConstantNode(false), new ConstantNode(1), new ConstantNode(2))),
        );
    }

    public function getCompileData()
    {
        return array(
            array('((true) ? (1) : (2))', new ConditionalNode(new ConstantNode(true), new ConstantNode(1), new ConstantNode(2))),
            array('((false) ? (1) : (2))', new ConditionalNode(new ConstantNode(false), new ConstantNode(1), new ConstantNode(2))),
        );
    }

    public function getDumpData()
    {
        return array(
            array('(true ? 1 : 2)', new ConditionalNode(new ConstantNode(true), new ConstantNode(1), new ConstantNode(2))),
            array('(false ? 1 : 2)', new ConditionalNode(new ConstantNode(false), new ConstantNode(1), new ConstantNode(2))),
        );
    }
}
