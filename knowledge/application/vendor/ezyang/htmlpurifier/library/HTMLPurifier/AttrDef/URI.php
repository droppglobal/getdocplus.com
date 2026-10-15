<?php

/**
 * Validates a URI as defined by RFC 3986.
 * @note Scheme-specific mechanics deferred to HTMLPurifier_URIScheme
 */
class HTMLPurifier_AttrDef_URI extends HTMLPurifier_AttrDef
{

    /**
     * @type HTMLPurifier_URIParser
     */
    protected $parser;

    /**
     * @type bool
     */
    protected $embedsResource;

    /**
     * @param bool $embeds_resource Does the URI here result in an extra HTTP request?
     */
    public function __construct($embeds_resource = false)
    {
        $this->parser = new HTMLPurifier_URIParser();
        $this->embedsResource = (bool)$embeds_resource;
    }

    /**
     * @param string $string
     * @return HTMLPurifier_AttrDef_URI
     */
    public function make($string)
    {
        $embeds = ($string === 'embedded');
        return new HTMLPurifier_AttrDef_URI($embeds);
    }

    /**
     * @param string $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($uri, $config, $context)
    {
        if ($config->get('URI.Disable')) {
            return false;
        }

        $uri = $this->parseCDATA($uri);

        
        $uri = $this->parser->parse($uri);
        if ($uri === false) {
            return false;
        }

        
        $context->register('EmbeddedURI', $this->embedsResource);

        $ok = false;
        do {

            
            $result = $uri->validate($config, $context);
            if (!$result) {
                break;
            }

            
            $uri_def = $config->getDefinition('URI');
            $result = $uri_def->filter($uri, $config, $context);
            if (!$result) {
                break;
            }

            
            $scheme_obj = $uri->getSchemeObj($config, $context);
            if (!$scheme_obj) {
                break;
            }
            if ($this->embedsResource && !$scheme_obj->browsable) {
                break;
            }
            $result = $scheme_obj->validate($uri, $config, $context);
            if (!$result) {
                break;
            }

            
            $result = $uri_def->postFilter($uri, $config, $context);
            if (!$result) {
                break;
            }

            
            $ok = true;

        } while (false);

        $context->destroy('EmbeddedURI');
        if (!$ok) {
            return false;
        }
        
        return $uri->toString();
    }
}


