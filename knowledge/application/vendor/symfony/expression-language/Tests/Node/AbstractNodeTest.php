<?php



namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Compiler;

abstract class AbstractNodeTest extends TestCase
{
    /**
     * @dataProvider getEvaluateData
     */
    public function testEvaluate($expected, $node, $variables = array(), $functions = array())
    {
        $this->assertSame($expected, $node->evaluate($functions, $variables));
    }

    abstract public function getEvaluateData();

    /**
     * @dataProvider getCompileData
     */
    public function testCompile($expected, $node, $functions = array())
    {
        $compiler = new Compiler($functions);
        $node->compile($compiler);
        $this->assertSame($expected, $compiler->getSource());
    }

    abstract public function getCompileData();

    /**
     * @dataProvider getDumpData
     */
    public function testDump($expected, $node)
    {
        $this->assertSame($expected, $node->dump());
    }

    abstract public function getDumpData();
}
