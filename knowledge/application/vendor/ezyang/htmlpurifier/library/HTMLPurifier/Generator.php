<?php

/**
 * Generates HTML from tokens.
 * @todo Refactor interface so that configuration/context is determined
 *       upon instantiation, no need for messy generateFromTokens() calls
 * @todo Make some of the more internal functions protected, and have
 *       unit tests work around that
 */
class HTMLPurifier_Generator
{

    /**
     * Whether or not generator should produce XML output.
     * @type bool
     */
    private $_xhtml = true;

    /**
     * :HACK: Whether or not generator should comment the insides of <script> tags.
     * @type bool
     */
    private $_scriptFix = false;

    /**
     * Cache of HTMLDefinition during HTML output to determine whether or
     * not attributes should be minimized.
     * @type HTMLPurifier_HTMLDefinition
     */
    private $_def;

    /**
     * Cache of %Output.SortAttr.
     * @type bool
     */
    private $_sortAttr;

    /**
     * Cache of %Output.FlashCompat.
     * @type bool
     */
    private $_flashCompat;

    /**
     * Cache of %Output.FixInnerHTML.
     * @type bool
     */
    private $_innerHTMLFix;

    /**
     * Stack for keeping track of object information when outputting IE
     * compatibility code.
     * @type array
     */
    private $_flashStack = array();

    /**
     * Configuration for the generator
     * @type HTMLPurifier_Config
     */
    protected $config;

    /**
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     */
    public function __construct($config, $context)
    {
        $this->config = $config;
        $this->_scriptFix = $config->get('Output.CommentScriptContents');
        $this->_innerHTMLFix = $config->get('Output.FixInnerHTML');
        $this->_sortAttr = $config->get('Output.SortAttr');
        $this->_flashCompat = $config->get('Output.FlashCompat');
        $this->_def = $config->getHTMLDefinition();
        $this->_xhtml = $this->_def->doctype->xml;
    }

    /**
     * Generates HTML from an array of tokens.
     * @param HTMLPurifier_Token[] $tokens Array of HTMLPurifier_Token
     * @return string Generated HTML
     */
    public function generateFromTokens($tokens)
    {
        if (!$tokens) {
            return '';
        }

        
        $html = '';
        for ($i = 0, $size = count($tokens); $i < $size; $i++) {
            if ($this->_scriptFix && $tokens[$i]->name === 'script'
                && $i + 2 < $size && $tokens[$i+2] instanceof HTMLPurifier_Token_End) {
                
                
                
                $html .= $this->generateFromToken($tokens[$i++]);
                $html .= $this->generateScriptFromToken($tokens[$i++]);
            }
            $html .= $this->generateFromToken($tokens[$i]);
        }

        
        if (extension_loaded('tidy') && $this->config->get('Output.TidyFormat')) {
            $tidy = new Tidy;
            $tidy->parseString(
                $html,
                array(
                   'indent'=> true,
                   'output-xhtml' => $this->_xhtml,
                   'show-body-only' => true,
                   'indent-spaces' => 2,
                   'wrap' => 68,
                ),
                'utf8'
            );
            $tidy->cleanRepair();
            $html = (string) $tidy; 
        }

        
        if ($this->config->get('Core.NormalizeNewlines')) {
            $nl = $this->config->get('Output.Newline');
            if ($nl === null) {
                $nl = PHP_EOL;
            }
            if ($nl !== "\n") {
                $html = str_replace("\n", $nl, $html);
            }
        }
        return $html;
    }

    /**
     * Generates HTML from a single token.
     * @param HTMLPurifier_Token $token HTMLPurifier_Token object.
     * @return string Generated HTML
     */
    public function generateFromToken($token)
    {
        if (!$token instanceof HTMLPurifier_Token) {
            trigger_error('Cannot generate HTML from non-HTMLPurifier_Token object', E_USER_WARNING);
            return '';

        } elseif ($token instanceof HTMLPurifier_Token_Start) {
            $attr = $this->generateAttributes($token->attr, $token->name);
            if ($this->_flashCompat) {
                if ($token->name == "object") {
                    $flash = new stdClass();
                    $flash->attr = $token->attr;
                    $flash->param = array();
                    $this->_flashStack[] = $flash;
                }
            }
            return '<' . $token->name . ($attr ? ' ' : '') . $attr . '>';

        } elseif ($token instanceof HTMLPurifier_Token_End) {
            $_extra = '';
            if ($this->_flashCompat) {
                if ($token->name == "object" && !empty($this->_flashStack)) {
                    
                }
            }
            return $_extra . '</' . $token->name . '>';

        } elseif ($token instanceof HTMLPurifier_Token_Empty) {
            if ($this->_flashCompat && $token->name == "param" && !empty($this->_flashStack)) {
                $this->_flashStack[count($this->_flashStack)-1]->param[$token->attr['name']] = $token->attr['value'];
            }
            $attr = $this->generateAttributes($token->attr, $token->name);
             return '<' . $token->name . ($attr ? ' ' : '') . $attr .
                ( $this->_xhtml ? ' /': '' ) 
                . '>';

        } elseif ($token instanceof HTMLPurifier_Token_Text) {
            return $this->escape($token->data, ENT_NOQUOTES);

        } elseif ($token instanceof HTMLPurifier_Token_Comment) {
            return '<!--' . $token->data . '-->';
        } else {
            return '';

        }
    }

    /**
     * Special case processor for the contents of script tags
     * @param HTMLPurifier_Token $token HTMLPurifier_Token object.
     * @return string
     * @warning This runs into problems if there's already a literal
     *          --> somewhere inside the script contents.
     */
    public function generateScriptFromToken($token)
    {
        if (!$token instanceof HTMLPurifier_Token_Text) {
            return $this->generateFromToken($token);
        }
        
        $data = preg_replace('#//\s*$#', '', $token->data);
        return '<!--//--><![CDATA[//><!--' . "\n" . trim($data) . "\n" . '//--><!]]>';
    }

    /**
     * Generates attribute declarations from attribute array.
     * @note This does not include the leading or trailing space.
     * @param array $assoc_array_of_attributes Attribute array
     * @param string $element Name of element attributes are for, used to check
     *        attribute minimization.
     * @return string Generated HTML fragment for insertion.
     */
    public function generateAttributes($assoc_array_of_attributes, $element = '')
    {
        $html = '';
        if ($this->_sortAttr) {
            ksort($assoc_array_of_attributes);
        }
        foreach ($assoc_array_of_attributes as $key => $value) {
            if (!$this->_xhtml) {
                
                if (strpos($key, ':') !== false) {
                    continue;
                }
                
                if ($element && !empty($this->_def->info[$element]->attr[$key]->minimized)) {
                    $html .= $key . ' ';
                    continue;
                }
            }
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            
            if ($this->_innerHTMLFix) {
                if (strpos($value, '`') !== false) {
                    
                    
                    if (strcspn($value, '"\' <>') === strlen($value)) {
                        
                        $value .= ' ';
                    }
                }
            }
            $html .= $key.'="'.$this->escape($value).'" ';
        }
        return rtrim($html);
    }

    /**
     * Escapes raw text data.
     * @todo This really ought to be protected, but until we have a facility
     *       for properly generating HTML here w/o using tokens, it stays
     *       public.
     * @param string $string String data to escape for HTML.
     * @param int $quote Quoting style, like htmlspecialchars. ENT_NOQUOTES is
     *               permissible for non-attribute output.
     * @return string escaped data.
     */
    public function escape($string, $quote = null)
    {
        
        
        if ($quote === null) {
            $quote = ENT_COMPAT;
        }
        return htmlspecialchars($string, $quote, 'UTF-8');
    }
}


