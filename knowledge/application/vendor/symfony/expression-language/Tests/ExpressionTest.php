<?php



namespace Symfony\Component\ExpressionLanguage\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;

class ExpressionTest extends TestCase
{
    public function testSerialization()
    {
        $expression = new Expression('kernel.boot()');

        $serializedExpression = serialize($expression);
        $unserializedExpression = unserialize($serializedExpression);

        $this->assertEquals($expression, $unserializedExpression);
    }
}
