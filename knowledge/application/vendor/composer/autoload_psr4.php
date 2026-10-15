<?php



$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Symfony\\Polyfill\\Php80\\' => array($vendorDir . '/symfony/polyfill-php80'),
    'Symfony\\Polyfill\\Apcu\\' => array($vendorDir . '/symfony/polyfill-apcu'),
    'Symfony\\Component\\Finder\\' => array($vendorDir . '/symfony/finder'),
    'Symfony\\Component\\ExpressionLanguage\\' => array($vendorDir . '/symfony/expression-language'),
    'Symfony\\Component\\Cache\\' => array($vendorDir . '/symfony/cache'),
    'Stripe\\' => array($vendorDir . '/stripe/stripe-php/lib'),
    'ScssPhp\\ScssPhp\\' => array($vendorDir . '/scssphp/scssphp/src'),
    'Psr\\SimpleCache\\' => array($vendorDir . '/psr/simple-cache/src'),
    'Psr\\Log\\' => array($vendorDir . '/psr/log/Psr/Log'),
    'Psr\\Cache\\' => array($vendorDir . '/psr/cache/src'),
    'MoTranslator\\' => array($vendorDir . '/phpmyadmin/motranslator/src'),
    'Gregwar\\' => array($vendorDir . '/gregwar/captcha/src/Gregwar'),
    'Facebook\\' => array($vendorDir . '/facebook/graph-sdk/src/Facebook'),
    'DrewM\\MailChimp\\' => array($vendorDir . '/drewm/mailchimp-api/src'),
);
