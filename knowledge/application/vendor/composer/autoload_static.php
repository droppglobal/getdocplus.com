<?php



namespace Composer\Autoload;

class ComposerStaticInit3fe2a76fc978856c350dfd5ebfe7e1f3
{
    public static $files = array (
        '32dcc8afd4335739640db7d200c1971d' => __DIR__ . '/..' . '/symfony/polyfill-apcu/bootstrap.php',
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        'c65d09b6820da036953a371c8c73a9b1' => __DIR__ . '/..' . '/facebook/graph-sdk/src/Facebook/polyfills.php',
        '2cffec82183ee1cea088009cef9a6fc3' => __DIR__ . '/..' . '/ezyang/htmlpurifier/library/HTMLPurifier.composer.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Apcu\\' => 22,
            'Symfony\\Component\\Finder\\' => 25,
            'Symfony\\Component\\ExpressionLanguage\\' => 37,
            'Symfony\\Component\\Cache\\' => 24,
            'Stripe\\' => 7,
            'ScssPhp\\ScssPhp\\' => 16,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Log\\' => 8,
            'Psr\\Cache\\' => 10,
        ),
        'M' => 
        array (
            'MoTranslator\\' => 13,
        ),
        'G' => 
        array (
            'Gregwar\\' => 8,
        ),
        'F' => 
        array (
            'Facebook\\' => 9,
        ),
        'D' => 
        array (
            'DrewM\\MailChimp\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Apcu\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-apcu',
        ),
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
        'Symfony\\Component\\ExpressionLanguage\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/expression-language',
        ),
        'Symfony\\Component\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/cache',
        ),
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'ScssPhp\\ScssPhp\\' => 
        array (
            0 => __DIR__ . '/..' . '/scssphp/scssphp/src',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'MoTranslator\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmyadmin/motranslator/src',
        ),
        'Gregwar\\' => 
        array (
            0 => __DIR__ . '/..' . '/gregwar/captcha/src/Gregwar',
        ),
        'Facebook\\' => 
        array (
            0 => __DIR__ . '/..' . '/facebook/graph-sdk/src/Facebook',
        ),
        'DrewM\\MailChimp\\' => 
        array (
            0 => __DIR__ . '/..' . '/drewm/mailchimp-api/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PayPal' => 
            array (
                0 => __DIR__ . '/..' . '/paypal/rest-api-sdk-php/lib',
            ),
        ),
        'L' => 
        array (
            'Less' => 
            array (
                0 => __DIR__ . '/..' . '/oyejorge/less.php/lib',
            ),
        ),
        'J' => 
        array (
            'JShrink' => 
            array (
                0 => __DIR__ . '/..' . '/tedivm/jshrink/src',
            ),
        ),
        'H' => 
        array (
            'Hybrid' => 
            array (
                0 => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth',
            ),
            'HTMLPurifier' => 
            array (
                0 => __DIR__ . '/..' . '/ezyang/htmlpurifier/library',
            ),
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'EnvatoOAuth2Client' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/Envato/Envato.php',
        'Hybrid_Auth' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php',
        'Hybrid_Endpoint' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Endpoint.php',
        'Hybrid_Error' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Error.php',
        'Hybrid_Exception' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Exception.php',
        'Hybrid_Logger' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Logger.php',
        'Hybrid_Provider_Adapter' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Adapter.php',
        'Hybrid_Provider_Model' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model.php',
        'Hybrid_Provider_Model_OAuth1' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model_OAuth1.php',
        'Hybrid_Provider_Model_OAuth2' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model_OAuth2.php',
        'Hybrid_Provider_Model_OpenID' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Provider_Model_OpenID.php',
        'Hybrid_Providers_AOL' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/AOL.php',
        'Hybrid_Providers_Discord' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Discord.php',
        'Hybrid_Providers_Envato' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Envato.php',
        'Hybrid_Providers_Facebook' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Facebook.php',
        'Hybrid_Providers_Foursquare' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Foursquare.php',
        'Hybrid_Providers_Google' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Google.php',
        'Hybrid_Providers_LinkedIn' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/LinkedIn.php',
        'Hybrid_Providers_Live' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Live.php',
        'Hybrid_Providers_OpenID' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/OpenID.php',
        'Hybrid_Providers_Paypal' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Paypal.php',
        'Hybrid_Providers_Twitter' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Twitter.php',
        'Hybrid_Providers_Yahoo' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Providers/Yahoo.php',
        'Hybrid_Storage' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/Storage.php',
        'Hybrid_Storage_Interface' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/StorageInterface.php',
        'Hybrid_User' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/User.php',
        'Hybrid_User_Activity' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/User_Activity.php',
        'Hybrid_User_Contact' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/User_Contact.php',
        'Hybrid_User_Profile' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/User_Profile.php',
        'LightOpenID' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OpenID/LightOpenID.php',
        'OAuth1Client' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth1Client.php',
        'OAuth2Client' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth2Client.php',
        'OAuthConsumer' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthDataStore' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthException' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthRequest' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthServer' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod_HMAC_SHA1' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod_PLAINTEXT' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthSignatureMethod_RSA_SHA1' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthToken' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'OAuthUtil' => __DIR__ . '/..' . '/hybridauth/hybridauth/hybridauth/Hybrid/thirdparty/OAuth/OAuth.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
        'lessc' => __DIR__ . '/..' . '/oyejorge/less.php/lessc.inc.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3fe2a76fc978856c350dfd5ebfe7e1f3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3fe2a76fc978856c350dfd5ebfe7e1f3::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3fe2a76fc978856c350dfd5ebfe7e1f3::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit3fe2a76fc978856c350dfd5ebfe7e1f3::$classMap;

        }, null, ClassLoader::class);
    }
}
