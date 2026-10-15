<?php

/**
 * XHTML 1.1 List Module, defines list-oriented elements. Core Module.
 */
class HTMLPurifier_HTMLModule_List extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'List';

    
    
    
    
    
    
    
    

    /**
     * @type array
     */
    public $content_sets = array('Flow' => 'List');

    /**
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $ol = $this->addElement('ol', 'List', new HTMLPurifier_ChildDef_List(), 'Common');
        $ul = $this->addElement('ul', 'List', new HTMLPurifier_ChildDef_List(), 'Common');
        
        
        
        
        
        
        $ol->wrap = 'li';
        $ul->wrap = 'li';
        $this->addElement('dl', 'List', 'Required: dt | dd', 'Common');

        $this->addElement('li', false, 'Flow', 'Common');

        $this->addElement('dd', false, 'Flow', 'Common');
        $this->addElement('dt', false, 'Inline', 'Common');
    }
}


