<?php



namespace Symfony\Component\ExpressionLanguage\Tests\Fixtures;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionPhpFunction;

class TestProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return array(
            new ExpressionFunction('identity', function ($input) {
                return $input;
            }, function (array $values, $input) {
                return $input;
            }),

            ExpressionFunction::fromPhp('strtoupper'),

            ExpressionFunction::fromPhp('\strtolower'),

            ExpressionFunction::fromPhp('Symfony\Component\ExpressionLanguage\Tests\Fixtures\fn_namespaced', 'fn_namespaced'),
        );
    }
}

function fn_namespaced()
{
    return true;
}
