<?php

/**
 * Implements safety checks for safe iframes.
 *
 * @warning This filter is *critical* for ensuring that %HTML.SafeIframe
 * works safely.
 */
class HTMLPurifier_URIFilter_SafeIframe extends HTMLPurifier_URIFilter
{
    /**
     * @type string
     */
    public $name = 'SafeIframe';

    /**
     * @type bool
     */
    public $always_load = true;

    /**
     * @type string
     */
    protected $regexp = null;

    
    
    
    /**
     * @param HTMLPurifier_Config $config
     * @return bool
     */
    public function prepare($config)
    {
        $this->regexp = $config->get('URI.SafeIframeRegexp');
        return true;
    }

    /**
     * @param HTMLPurifier_URI $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function filter(&$uri, $config, $context)
    {
        
        if (!$config->get('HTML.SafeIframe')) {
            return true;
        }
        
        if (!$context->get('EmbeddedURI', true)) {
            return true;
        }
        $token = $context->get('CurrentToken', true);
        if (!($token && $token->name == 'iframe')) {
            return true;
        }
        
        if ($this->regexp === null) {
            return false;
        }
        
        return preg_match($this->regexp, $uri->toString());
    }
}


