<?php


use MoTranslator\Loader;

/**
 * Sets a requested locale.
 *
 * @param int    $category Locale category, ignored
 * @param string $locale   Locale name
 *
 * @return string Set or current locale
 */
function _setlocale($category, $locale)
{
    return Loader::getInstance()->setlocale($locale);
}

/**
 * Sets the path for a domain.
 *
 * @param string $domain Domain name
 * @param string $path   Path where to find locales
 */
function _bindtextdomain($domain, $path)
{
    Loader::getInstance()->bindtextdomain($domain, $path);
}

/**
 * Dummy compatibility function, MoTranslator assumes
 * everything is UTF-8.
 */
function _bind_textdomain_codeset($domain, $codeset)
{
    return;
}

/**
 * Sets the default domain.
 *
 * @param string $domain Domain name
 */
function _textdomain($domain)
{
    Loader::getInstance()->textdomain($domain);
}

/**
 * Translates a string.
 *
 * @param string $msgid String to be translated
 *
 * @return string translated string (or original, if not found)
 */
function _gettext($msgid)
{
    return Loader::getInstance()->getTranslator()->gettext(
        $msgid
    );
}

/**
 * Translates a string, alias for _gettext.
 *
 * @param string $msgid String to be translated
 *
 * @return string translated string (or original, if not found)
 */
function __($msgid)
{
    return Loader::getInstance()->getTranslator()->gettext(
        $msgid
    );
}

/**
 * Plural version of gettext.
 *
 * @param string $msgid       Single form
 * @param string $msgidPlural Plural form
 * @param int    $number      Number of objects
 *
 * @return string translated plural form
 */
function _ngettext($msgid, $msgidPlural, $number)
{
    return Loader::getInstance()->getTranslator()->ngettext(
        $msgid, $msgidPlural, $number
    );
}

/**
 * Translate with context.
 *
 * @param string $msgctxt Context
 * @param string $msgid   String to be translated
 *
 * @return string translated plural form
 */
function _pgettext($msgctxt, $msgid)
{
    return Loader::getInstance()->getTranslator()->pgettext(
        $msgctxt, $msgid
    );
}

/**
 * Plural version of pgettext.
 *
 * @param string $msgctxt     Context
 * @param string $msgid       Single form
 * @param string $msgidPlural Plural form
 * @param int    $number      Number of objects
 *
 * @return string translated plural form
 */
function _npgettext($msgctxt, $msgid, $msgidPlural, $number)
{
    return Loader::getInstance()->getTranslator()->npgettext(
        $msgctxt, $msgid, $msgidPlural, $number
    );
}

/**
 * Translates a string.
 *
 * @param string $domain Domain to use
 * @param string $msgid  String to be translated
 *
 * @return string translated string (or original, if not found)
 */
function _dgettext($domain, $msgid)
{
    return Loader::getInstance()->getTranslator($domain)->gettext(
        $msgid
    );
}

/**
 * Plural version of gettext.
 *
 * @param string $domain      Domain to use
 * @param string $msgid       Single form
 * @param string $msgidPlural Plural form
 * @param int    $number      Number of objects
 *
 * @return string translated plural form
 */
function _dngettext($domain, $msgid, $msgidPlural, $number)
{
    return Loader::getInstance()->getTranslator($domain)->ngettext(
        $msgid, $msgidPlural, $number
    );
}

/**
 * Translate with context.
 *
 * @param string $domain  Domain to use
 * @param string $msgctxt Context
 * @param string $msgid   String to be translated
 *
 * @return string translated plural form
 */
function _dpgettext($domain, $msgctxt, $msgid)
{
    return Loader::getInstance()->getTranslator($domain)->pgettext(
        $msgctxt, $msgid
    );
}

/**
 * Plural version of pgettext.
 *
 * @param string $domain      Domain to use
 * @param string $msgctxt     Context
 * @param string $msgid       Single form
 * @param string $msgidPlural Plural form
 * @param int    $number      Number of objects
 *
 * @return string translated plural form
 */
function _dnpgettext($domain, $msgctxt, $msgid, $msgidPlural, $number)
{
    return Loader::getInstance()->getTranslator($domain)->npgettext(
        $msgctxt, $msgid, $msgidPlural, $number
    );
}
