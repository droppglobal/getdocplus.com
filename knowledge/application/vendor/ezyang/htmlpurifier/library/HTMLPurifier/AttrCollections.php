<?php

/**
 * Defines common attribute collections that modules reference
 */

class HTMLPurifier_AttrCollections
{

    /**
     * Associative array of attribute collections, indexed by name.
     * @type array
     */
    public $info = array();

    /**
     * Performs all expansions on internal data for use by other inclusions
     * It also collects all attribute collection extensions from
     * modules
     * @param HTMLPurifier_AttrTypes $attr_types HTMLPurifier_AttrTypes instance
     * @param HTMLPurifier_HTMLModule[] $modules Hash array of HTMLPurifier_HTMLModule members
     */
    public function __construct($attr_types, $modules)
    {
        $this->doConstruct($attr_types, $modules);
    }

    public function doConstruct($attr_types, $modules)
    {
        
        foreach ($modules as $module) {
            foreach ($module->attr_collections as $coll_i => $coll) {
                if (!isset($this->info[$coll_i])) {
                    $this->info[$coll_i] = array();
                }
                foreach ($coll as $attr_i => $attr) {
                    if ($attr_i === 0 && isset($this->info[$coll_i][$attr_i])) {
                        
                        $this->info[$coll_i][$attr_i] = array_merge(
                            $this->info[$coll_i][$attr_i],
                            $attr
                        );
                        continue;
                    }
                    $this->info[$coll_i][$attr_i] = $attr;
                }
            }
        }
        
        foreach ($this->info as $name => $attr) {
            
            $this->performInclusions($this->info[$name]);
            
            $this->expandIdentifiers($this->info[$name], $attr_types);
        }
    }

    /**
     * Takes a reference to an attribute associative array and performs
     * all inclusions specified by the zero index.
     * @param array &$attr Reference to attribute array
     */
    public function performInclusions(&$attr)
    {
        if (!isset($attr[0])) {
            return;
        }
        $merge = $attr[0];
        $seen  = array(); 
        
        for ($i = 0; isset($merge[$i]); $i++) {
            if (isset($seen[$merge[$i]])) {
                continue;
            }
            $seen[$merge[$i]] = true;
            
            if (!isset($this->info[$merge[$i]])) {
                continue;
            }
            foreach ($this->info[$merge[$i]] as $key => $value) {
                if (isset($attr[$key])) {
                    continue;
                } 
                $attr[$key] = $value;
            }
            if (isset($this->info[$merge[$i]][0])) {
                
                $merge = array_merge($merge, $this->info[$merge[$i]][0]);
            }
        }
        unset($attr[0]);
    }

    /**
     * Expands all string identifiers in an attribute array by replacing
     * them with the appropriate values inside HTMLPurifier_AttrTypes
     * @param array &$attr Reference to attribute array
     * @param HTMLPurifier_AttrTypes $attr_types HTMLPurifier_AttrTypes instance
     */
    public function expandIdentifiers(&$attr, $attr_types)
    {
        
        
        $processed = array();

        foreach ($attr as $def_i => $def) {
            
            if ($def_i === 0) {
                continue;
            }

            if (isset($processed[$def_i])) {
                continue;
            }

            
            if ($required = (strpos($def_i, '*') !== false)) {
                
                unset($attr[$def_i]);
                $def_i = trim($def_i, '*');
                $attr[$def_i] = $def;
            }

            $processed[$def_i] = true;

            
            if (is_object($def)) {
                
                $attr[$def_i]->required = ($required || $attr[$def_i]->required);
                continue;
            }

            if ($def === false) {
                unset($attr[$def_i]);
                continue;
            }

            if ($t = $attr_types->get($def)) {
                $attr[$def_i] = $t;
                $attr[$def_i]->required = $required;
            } else {
                unset($attr[$def_i]);
            }
        }
    }
}


