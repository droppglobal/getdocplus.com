<?php

class HTMLPurifier_HTMLModule_CommonAttributes extends HTMLPurifier_HTMLModule
{
    /**
     * @type string
     */
    public $name = 'CommonAttributes';

    /**
     * @type array
     */
    public $attr_collections = array(
        'Core' => array(
            0 => array('Style'),
            
            'class' => 'Class',
            'id' => 'ID',
            'title' => 'CDATA',
            'contenteditable' => 'ContentEditable',
        ),
        'Lang' => array(),
        'I18N' => array(
            0 => array('Lang'), 
        ),
        'Common' => array(
            0 => array('Core', 'I18N')
        )
    );
}


