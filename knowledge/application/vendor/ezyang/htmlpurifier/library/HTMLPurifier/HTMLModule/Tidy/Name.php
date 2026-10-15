<?php

/**
 * Name is deprecated, but allowed in strict doctypes, so onl
 */
class HTMLPurifier_HTMLModule_Tidy_Name extends HTMLPurifier_HTMLModule_Tidy
{
    /**
     * @type string
     */
    public $name = 'Tidy_Name';

    /**
     * @type string
     */
    public $defaultLevel = 'heavy';

    /**
     * @return array
     */
    public function makeFixes()
    {
        $r = array();
        
        
        
        $r['img@name'] =
        $r['a@name'] = new HTMLPurifier_AttrTransform_Name();
        return $r;
    }
}


