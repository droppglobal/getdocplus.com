<?php



namespace Symfony\Component\ExpressionLanguage\ParserCache;

@trigger_error('The '.__NAMESPACE__.'\ArrayParserCache class is deprecated since version 3.2 and will be removed in 4.0. Use the Symfony\Component\Cache\Adapter\ArrayAdapter class instead.', E_USER_DEPRECATED);

use Symfony\Component\ExpressionLanguage\ParsedExpression;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 *
 * @deprecated ArrayParserCache class is deprecated since version 3.2 and will be removed in 4.0. Use the Symfony\Component\Cache\Adapter\ArrayAdapter class instead.
 */
class ArrayParserCache implements ParserCacheInterface
{
    private $cache = array();

    /**
     * {@inheritdoc}
     */
    public function fetch($key)
    {
        return isset($this->cache[$key]) ? $this->cache[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function save($key, ParsedExpression $expression)
    {
        $this->cache[$key] = $expression;
    }
}
