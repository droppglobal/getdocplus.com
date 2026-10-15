<?php


if (class_exists('Requests') === false)
{
    require_once __DIR__.'/libs/Requests-1.7.0/library/Requests.php';
}

try
{
    Requests::register_autoloader();

    if (version_compare(Requests::VERSION, '1.6.0') === -1)
    {
        throw new Exception('Requests class found but did not match');
    }
}
catch (\Exception $e)
{
    throw new Exception('Requests class found but did not match');
}

spl_autoload_register(function ($class)
{
    
    $prefix = 'Razorpay\Api';

    
    $base_dir = __DIR__ . '/src/';

    
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0)
    {
        
        return;
    }

    
    $relative_class = substr($class, $len);

    
    
    
    
    
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    
    if (file_exists($file))
    {
        require $file;
    }
});