<?php


if (!defined('HTMLPURIFIER_PREFIX')) {
    define('HTMLPURIFIER_PREFIX', realpath(dirname(__FILE__) . '/..'));
}



if (!defined('PHP_EOL')) {
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        case 'WIN':
            define('PHP_EOL', "\r\n");
            break;
        case 'DAR':
            define('PHP_EOL', "\r");
            break;
        default:
            define('PHP_EOL', "\n");
    }
}

/**
 * Bootstrap class that contains meta-functionality for HTML Purifier such as
 * the autoload function.
 *
 * @note
 *      This class may be used without any other files from HTML Purifier.
 */
class HTMLPurifier_Bootstrap
{

    /**
     * Autoload function for HTML Purifier
     * @param string $class Class to load
     * @return bool
     */
    public static function autoload($class)
    {
        $file = HTMLPurifier_Bootstrap::getPath($class);
        if (!$file) {
            return false;
        }
        
        
        
        
        
        require_once HTMLPURIFIER_PREFIX . '/' . $file;
        return true;
    }

    /**
     * Returns the path for a specific class.
     * @param string $class Class path to get
     * @return string
     */
    public static function getPath($class)
    {
        if (strncmp('HTMLPurifier', $class, 12) !== 0) {
            return false;
        }
        
        if (strncmp('HTMLPurifier_Language_', $class, 22) === 0) {
            $code = str_replace('_', '-', substr($class, 22));
            $file = 'HTMLPurifier/Language/classes/' . $code . '.php';
        } else {
            $file = str_replace('_', '/', $class) . '.php';
        }
        if (!file_exists(HTMLPURIFIER_PREFIX . '/' . $file)) {
            return false;
        }
        return $file;
    }

    /**
     * "Pre-registers" our autoloader on the SPL stack.
     */
    public static function registerAutoload()
    {
        $autoload = array('HTMLPurifier_Bootstrap', 'autoload');
        if (spl_autoload_functions() === false) {
            spl_autoload_register($autoload);
        } else {
            
            spl_autoload_register($autoload, true, true);
        }
    }
}


