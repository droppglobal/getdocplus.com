<?php

/**
 * Definition that allows a set of elements, but disallows empty children.
 */
class HTMLPurifier_ChildDef_Required extends HTMLPurifier_ChildDef
{
    /**
     * Lookup table of allowed elements.
     * @type array
     */
    public $elements = array();

    /**
     * Whether or not the last passed node was all whitespace.
     * @type bool
     */
    protected $whitespace = false;

    /**
     * @param array|string $elements List of allowed element names (lowercase).
     */
    public function __construct($elements)
    {
        if (is_string($elements)) {
            $elements = str_replace(' ', '', $elements);
            $elements = explode('|', $elements);
        }
        $keys = array_keys($elements);
        if ($keys == array_keys($keys)) {
            $elements = array_flip($elements);
            foreach ($elements as $i => $x) {
                $elements[$i] = true;
                if (empty($i)) {
                    unset($elements[$i]);
                } 
            }
        }
        $this->elements = $elements;
    }

    /**
     * @type bool
     */
    public $allow_empty = false;

    /**
     * @type string
     */
    public $type = 'required';

    /**
     * @param array $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function validateChildren($children, $config, $context)
    {
        
        $this->whitespace = false;

        
        if (empty($children)) {
            return false;
        }

        
        $result = array();

        
        
        
        $pcdata_allowed = isset($this->elements['#PCDATA']);

        
        $all_whitespace = true;

        $stack = array_reverse($children);
        while (!empty($stack)) {
            $node = array_pop($stack);
            if (!empty($node->is_whitespace)) {
                $result[] = $node;
                continue;
            }
            $all_whitespace = false; 

            if (!isset($this->elements[$node->name])) {
                
                
                if ($pcdata_allowed && $node instanceof HTMLPurifier_Node_Text) {
                    $result[] = $node;
                    continue;
                }
                
                
                if ($node instanceof HTMLPurifier_Node_Element) {
                    for ($i = count($node->children) - 1; $i >= 0; $i--) {
                        $stack[] = $node->children[$i];
                    }
                    continue;
                }
                continue;
            }
            $result[] = $node;
        }
        if (empty($result)) {
            return false;
        }
        if ($all_whitespace) {
            $this->whitespace = true;
            return false;
        }
        return $result;
    }
}


