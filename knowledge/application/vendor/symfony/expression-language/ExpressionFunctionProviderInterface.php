<?php



namespace Symfony\Component\ExpressionLanguage;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface ExpressionFunctionProviderInterface
{
    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions();
}
