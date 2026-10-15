<?php

class HTMLPurifier_HTMLModuleManager
{

    /**
     * @type HTMLPurifier_DoctypeRegistry
     */
    public $doctypes;

    /**
     * Instance of current doctype.
     * @type string
     */
    public $doctype;

    /**
     * @type HTMLPurifier_AttrTypes
     */
    public $attrTypes;

    /**
     * Active instances of modules for the specified doctype are
     * indexed, by name, in this array.
     * @type HTMLPurifier_HTMLModule[]
     */
    public $modules = array();

    /**
     * Array of recognized HTMLPurifier_HTMLModule instances,
     * indexed by module's class name. This array is usually lazy loaded, but a
     * user can overload a module by pre-emptively registering it.
     * @type HTMLPurifier_HTMLModule[]
     */
    public $registeredModules = array();

    /**
     * List of extra modules that were added by the user
     * using addModule(). These get unconditionally merged into the current doctype, whatever
     * it may be.
     * @type HTMLPurifier_HTMLModule[]
     */
    public $userModules = array();

    /**
     * Associative array of element name to list of modules that have
     * definitions for the element; this array is dynamically filled.
     * @type array
     */
    public $elementLookup = array();

    /**
     * List of prefixes we should use for registering small names.
     * @type array
     */
    public $prefixes = array('HTMLPurifier_HTMLModule_');

    /**
     * @type HTMLPurifier_ContentSets
     */
    public $contentSets;

    /**
     * @type HTMLPurifier_AttrCollections
     */
    public $attrCollections;

    /**
     * If set to true, unsafe elements and attributes will be allowed.
     * @type bool
     */
    public $trusted = false;

    public function __construct()
    {
        
        $this->attrTypes = new HTMLPurifier_AttrTypes();
        $this->doctypes  = new HTMLPurifier_DoctypeRegistry();

        
        $common = array(
            'CommonAttributes', 'Text', 'Hypertext', 'List',
            'Presentation', 'Edit', 'Bdo', 'Tables', 'Image',
            'StyleAttribute',
            
            'Scripting', 'Object', 'Forms',
            
            'Name',
        );
        $transitional = array('Legacy', 'Target', 'Iframe');
        $xml = array('XMLCommonAttributes');
        $non_xml = array('NonXMLCommonAttributes');

        
        $this->doctypes->register(
            'HTML 4.01 Transitional',
            false,
            array_merge($common, $transitional, $non_xml),
            array('Tidy_Transitional', 'Tidy_Proprietary'),
            array(),
            '-//W3C//DTD HTML 4.01 Transitional//EN',
            'http://www.w3.org/TR/html4/loose.dtd'
        );

        $this->doctypes->register(
            'HTML 4.01 Strict',
            false,
            array_merge($common, $non_xml),
            array('Tidy_Strict', 'Tidy_Proprietary', 'Tidy_Name'),
            array(),
            '-//W3C//DTD HTML 4.01//EN',
            'http://www.w3.org/TR/html4/strict.dtd'
        );

        $this->doctypes->register(
            'XHTML 1.0 Transitional',
            true,
            array_merge($common, $transitional, $xml, $non_xml),
            array('Tidy_Transitional', 'Tidy_XHTML', 'Tidy_Proprietary', 'Tidy_Name'),
            array(),
            '-//W3C//DTD XHTML 1.0 Transitional//EN',
            'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'
        );

        $this->doctypes->register(
            'XHTML 1.0 Strict',
            true,
            array_merge($common, $xml, $non_xml),
            array('Tidy_Strict', 'Tidy_XHTML', 'Tidy_Strict', 'Tidy_Proprietary', 'Tidy_Name'),
            array(),
            '-//W3C//DTD XHTML 1.0 Strict//EN',
            'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'
        );

        $this->doctypes->register(
            'XHTML 1.1',
            true,
            
            
            array_merge($common, $xml, array('Ruby', 'Iframe')),
            array('Tidy_Strict', 'Tidy_XHTML', 'Tidy_Proprietary', 'Tidy_Strict', 'Tidy_Name'), 
            array(),
            '-//W3C//DTD XHTML 1.1//EN',
            'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'
        );

    }

    /**
     * Registers a module to the recognized module list, useful for
     * overloading pre-existing modules.
     * @param $module Mixed: string module name, with or without
     *                HTMLPurifier_HTMLModule prefix, or instance of
     *                subclass of HTMLPurifier_HTMLModule.
     * @param $overload Boolean whether or not to overload previous modules.
     *                  If this is not set, and you do overload a module,
     *                  HTML Purifier will complain with a warning.
     * @note This function will not call autoload, you must instantiate
     *       (and thus invoke) autoload outside the method.
     * @note If a string is passed as a module name, different variants
     *       will be tested in this order:
     *          - Check for HTMLPurifier_HTMLModule_$name
     *          - Check all prefixes with $name in order they were added
     *          - Check for literal object name
     *          - Throw fatal error
     *       If your object name collides with an internal class, specify
     *       your module manually. All modules must have been included
     *       externally: registerModule will not perform inclusions for you!
     */
    public function registerModule($module, $overload = false)
    {
        if (is_string($module)) {
            
            $original_module = $module;
            $ok = false;
            foreach ($this->prefixes as $prefix) {
                $module = $prefix . $original_module;
                if (class_exists($module)) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                $module = $original_module;
                if (!class_exists($module)) {
                    trigger_error(
                        $original_module . ' module does not exist',
                        E_USER_ERROR
                    );
                    return;
                }
            }
            $module = new $module();
        }
        if (empty($module->name)) {
            trigger_error('Module instance of ' . get_class($module) . ' must have name');
            return;
        }
        if (!$overload && isset($this->registeredModules[$module->name])) {
            trigger_error('Overloading ' . $module->name . ' without explicit overload parameter', E_USER_WARNING);
        }
        $this->registeredModules[$module->name] = $module;
    }

    /**
     * Adds a module to the current doctype by first registering it,
     * and then tacking it on to the active doctype
     */
    public function addModule($module)
    {
        $this->registerModule($module);
        if (is_object($module)) {
            $module = $module->name;
        }
        $this->userModules[] = $module;
    }

    /**
     * Adds a class prefix that registerModule() will use to resolve a
     * string name to a concrete class
     */
    public function addPrefix($prefix)
    {
        $this->prefixes[] = $prefix;
    }

    /**
     * Performs processing on modules, after being called you may
     * use getElement() and getElements()
     * @param HTMLPurifier_Config $config
     */
    public function setup($config)
    {
        $this->trusted = $config->get('HTML.Trusted');

        
        $this->doctype = $this->doctypes->make($config);
        $modules = $this->doctype->modules;

        
        $lookup = $config->get('HTML.AllowedModules');
        $special_cases = $config->get('HTML.CoreModules');

        if (is_array($lookup)) {
            foreach ($modules as $k => $m) {
                if (isset($special_cases[$m])) {
                    continue;
                }
                if (!isset($lookup[$m])) {
                    unset($modules[$k]);
                }
            }
        }

        
        if ($config->get('HTML.Proprietary')) {
            $modules[] = 'Proprietary';
        }
        if ($config->get('HTML.SafeObject')) {
            $modules[] = 'SafeObject';
        }
        if ($config->get('HTML.SafeEmbed')) {
            $modules[] = 'SafeEmbed';
        }
        if ($config->get('HTML.SafeScripting') !== array()) {
            $modules[] = 'SafeScripting';
        }
        if ($config->get('HTML.Nofollow')) {
            $modules[] = 'Nofollow';
        }
        if ($config->get('HTML.TargetBlank')) {
            $modules[] = 'TargetBlank';
        }
        
        
        if ($config->get('HTML.TargetNoreferrer')) {
            $modules[] = 'TargetNoreferrer';
        }
        if ($config->get('HTML.TargetNoopener')) {
            $modules[] = 'TargetNoopener';
        }

        
        $modules = array_merge($modules, $this->userModules);

        foreach ($modules as $module) {
            $this->processModule($module);
            $this->modules[$module]->setup($config);
        }

        foreach ($this->doctype->tidyModules as $module) {
            $this->processModule($module);
            $this->modules[$module]->setup($config);
        }

        
        foreach ($this->modules as $module) {
            $n = array();
            foreach ($module->info_injector as $injector) {
                if (!is_object($injector)) {
                    $class = "HTMLPurifier_Injector_$injector";
                    $injector = new $class;
                }
                $n[$injector->name] = $injector;
            }
            $module->info_injector = $n;
        }

        
        foreach ($this->modules as $module) {
            foreach ($module->info as $name => $def) {
                if (!isset($this->elementLookup[$name])) {
                    $this->elementLookup[$name] = array();
                }
                $this->elementLookup[$name][] = $module->name;
            }
        }

        
        $this->contentSets = new HTMLPurifier_ContentSets(
            
            
            $this->modules
        );
        $this->attrCollections = new HTMLPurifier_AttrCollections(
            $this->attrTypes,
            
            
            
            $this->modules
        );
    }

    /**
     * Takes a module and adds it to the active module collection,
     * registering it if necessary.
     */
    public function processModule($module)
    {
        if (!isset($this->registeredModules[$module]) || is_object($module)) {
            $this->registerModule($module);
        }
        $this->modules[$module] = $this->registeredModules[$module];
    }

    /**
     * Retrieves merged element definitions.
     * @return Array of HTMLPurifier_ElementDef
     */
    public function getElements()
    {
        $elements = array();
        foreach ($this->modules as $module) {
            if (!$this->trusted && !$module->safe) {
                continue;
            }
            foreach ($module->info as $name => $v) {
                if (isset($elements[$name])) {
                    continue;
                }
                $elements[$name] = $this->getElement($name);
            }
        }

        
        
        foreach ($elements as $n => $v) {
            if ($v === false) {
                unset($elements[$n]);
            }
        }

        return $elements;

    }

    /**
     * Retrieves a single merged element definition
     * @param string $name Name of element
     * @param bool $trusted Boolean trusted overriding parameter: set to true
     *                 if you want the full version of an element
     * @return HTMLPurifier_ElementDef Merged HTMLPurifier_ElementDef
     * @note You may notice that modules are getting iterated over twice (once
     *       in getElements() and once here). This
     *       is because
     */
    public function getElement($name, $trusted = null)
    {
        if (!isset($this->elementLookup[$name])) {
            return false;
        }

        
        $def = false;
        if ($trusted === null) {
            $trusted = $this->trusted;
        }

        
        
        foreach ($this->elementLookup[$name] as $module_name) {
            $module = $this->modules[$module_name];

            
            
            if (!$trusted && !$module->safe) {
                continue;
            }

            
            
            
            $new_def = clone $module->info[$name];

            if (!$def && $new_def->standalone) {
                $def = $new_def;
            } elseif ($def) {
                
                
                $def->mergeIn($new_def);
            } else {
                
                
                
                
                
                
                
                
                
                
                continue;
            }

            
            $this->attrCollections->performInclusions($def->attr);
            $this->attrCollections->expandIdentifiers($def->attr, $this->attrTypes);

            
            if (is_string($def->content_model) &&
                strpos($def->content_model, 'Inline') !== false) {
                if ($name != 'del' && $name != 'ins') {
                    
                    $def->descendants_are_inline = true;
                }
            }

            $this->contentSets->generateChildDef($def, $module);
        }

        
        
        if (!$def) {
            return false;
        }

        
        foreach ($def->attr as $attr_name => $attr_def) {
            if ($attr_def->required) {
                $def->required_attr[] = $attr_name;
            }
        }
        return $def;
    }
}


